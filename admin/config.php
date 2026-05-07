<?php
/**
 * SpinGuard Admin — configuratie + security helpers
 *
 *  Bij eerste bezoek aan /admin/ wordt automatisch /admin/setup.php geopend
 *  zodat je een wachtwoord kunt instellen. Daarna wordt deze file
 *  bijgewerkt met de hash van je wachtwoord.
 *
 *  Wachtwoord vergeten? Verwijder de waarde tussen de quotes bij
 *  ADMIN_PASSWORD_HASH (laat dus '' staan) en ga opnieuw naar /admin/.
 *  Je kunt dan een nieuw wachtwoord instellen.
 */

const ADMIN_USERNAME      = 'admin';
const ADMIN_PASSWORD_HASH = '';  // ← wordt automatisch ingevuld door setup.php

const ADMIN_SESSION_LIFETIME = 28800;  // 8 uur max
const ADMIN_INACTIVITY_LIMIT = 3600;   // 1 uur idle = uitloggen
const MAX_LOGIN_ATTEMPTS     = 5;
const LOCKOUT_DURATION       = 900;    // 15 minuten

// === Pad helpers ===
function admin_log_dir() {
    $d = __DIR__ . '/../content/logs';
    if (!is_dir($d)) @mkdir($d, 0755, true);
    return $d;
}

function admin_base_url() {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/admin/index.php');
    $base = preg_replace('#/admin/[^/]+$#', '', $script);
    if ($base === '/' || $base === '.' || $base === false) $base = '';
    return rtrim($base, '/');
}

// === Sessiebeheer ===
function admin_session_start() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        $cookie_path = admin_base_url() . '/';
        if ($cookie_path === '') $cookie_path = '/';
        session_set_cookie_params([
            'lifetime' => ADMIN_SESSION_LIFETIME,
            'path'     => $cookie_path,
            'secure'   => !empty($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('SPINGUARD_ADMIN');
        session_start();
    }
}

function admin_needs_setup() {
    return ADMIN_PASSWORD_HASH === '' || strlen(ADMIN_PASSWORD_HASH) < 30;
}

function admin_is_logged_in() {
    admin_session_start();
    if (empty($_SESSION['admin_ok']) || $_SESSION['admin_ok'] !== true) return false;
    if (empty($_SESSION['admin_expires']) || $_SESSION['admin_expires'] <= time()) return false;
    if (!empty($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ADMIN_INACTIVITY_LIMIT) {
        admin_logout();
        return false;
    }
    if (!empty($_SESSION['ip_hash']) && $_SESSION['ip_hash'] !== _ip_hash()) {
        admin_logout();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

function admin_require_login() {
    if (admin_needs_setup()) {
        header('Location: ' . admin_base_url() . '/admin/setup.php');
        exit;
    }
    if (!admin_is_logged_in()) {
        header('Location: ' . admin_base_url() . '/admin/');
        exit;
    }
}

function admin_login($user, $pass) {
    if (admin_needs_setup()) return false;
    if (is_locked_out()) return false;
    if ($user !== ADMIN_USERNAME || !password_verify($pass, ADMIN_PASSWORD_HASH)) {
        record_failed_login();
        return false;
    }
    clear_failed_logins();
    admin_session_start();
    session_regenerate_id(true);
    $_SESSION['admin_ok']      = true;
    $_SESSION['admin_expires'] = time() + ADMIN_SESSION_LIFETIME;
    $_SESSION['last_activity'] = time();
    $_SESSION['ip_hash']       = _ip_hash();
    $_SESSION['login_time']    = time();
    log_activity('login', "Geslaagde login van " . _client_ip());
    return true;
}

function admin_logout() {
    admin_session_start();
    if (!empty($_SESSION['admin_ok'])) log_activity('logout', "Uitgelogd");
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

// === CSRF ===
function csrf_token() {
    admin_session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_check($t) {
    admin_session_start();
    return !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)($t ?? ''));
}

// === Brute-force bescherming ===
function _client_ip() { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }
function _ip_hash() { return hash('sha256', _client_ip() . ($_SERVER['HTTP_USER_AGENT'] ?? '')); }
function _lockout_file() { return admin_log_dir() . '/lockouts.json'; }

function _read_lockouts() {
    $f = _lockout_file();
    if (!is_file($f)) return [];
    $j = json_decode(@file_get_contents($f), true);
    return is_array($j) ? $j : [];
}
function _write_lockouts($data) {
    @file_put_contents(_lockout_file(), json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function record_failed_login() {
    $ip = _client_ip();
    $now = time();
    $data = _read_lockouts();
    if (!isset($data[$ip])) $data[$ip] = ['attempts' => [], 'locked_until' => 0];
    $data[$ip]['attempts'] = array_values(array_filter(
        $data[$ip]['attempts'], fn($t) => $now - $t < LOCKOUT_DURATION
    ));
    $data[$ip]['attempts'][] = $now;
    if (count($data[$ip]['attempts']) >= MAX_LOGIN_ATTEMPTS) {
        $data[$ip]['locked_until'] = $now + LOCKOUT_DURATION;
        log_activity('lockout', "IP $ip vergrendeld na " . MAX_LOGIN_ATTEMPTS . " mislukte pogingen");
    }
    log_activity('login_fail', "Mislukte login van $ip (poging " . count($data[$ip]['attempts']) . "/" . MAX_LOGIN_ATTEMPTS . ")");
    _write_lockouts($data);
}

function clear_failed_logins() {
    $ip = _client_ip();
    $data = _read_lockouts();
    if (isset($data[$ip])) { unset($data[$ip]); _write_lockouts($data); }
}

function is_locked_out() {
    $ip = _client_ip();
    $data = _read_lockouts();
    return isset($data[$ip]['locked_until']) && $data[$ip]['locked_until'] > time();
}

function lockout_seconds_remaining() {
    $ip = _client_ip();
    $data = _read_lockouts();
    if (!isset($data[$ip]['locked_until']) || $data[$ip]['locked_until'] <= time()) return 0;
    return $data[$ip]['locked_until'] - time();
}

// === Activity log ===
function log_activity($type, $message) {
    $line = sprintf("[%s] [%s] [%s] %s\n",
        date('Y-m-d H:i:s'), $type, _client_ip(),
        str_replace(["\n", "\r"], ' ', $message)
    );
    @file_put_contents(admin_log_dir() . '/activity.log', $line, FILE_APPEND | LOCK_EX);
}

function get_recent_activity($limit = 50) {
    $f = admin_log_dir() . '/activity.log';
    if (!is_file($f)) return [];
    $lines = file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_slice(array_reverse($lines), 0, $limit);
}
