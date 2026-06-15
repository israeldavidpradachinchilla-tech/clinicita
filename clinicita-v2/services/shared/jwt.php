<?php
class JWT {
    const SECRET = 'clinicita_ms_secret_2024';
    const TTL    = 7200; // 2 horas

    static function encode(array $payload): string {
        $h = self::b64u(json_encode(['alg'=>'HS256','typ'=>'JWT']));
        $p = self::b64u(json_encode($payload + ['iat'=>time(),'exp'=>time()+self::TTL]));
        $s = self::b64u(hash_hmac('sha256', "$h.$p", self::SECRET, true));
        return "$h.$p.$s";
    }

    static function decode(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;
        [$h, $p, $s] = $parts;
        $expected = self::b64u(hash_hmac('sha256', "$h.$p", self::SECRET, true));
        if (!hash_equals($expected, $s)) return null;
        $data = json_decode(self::b64d($p), true);
        if (!$data || ($data['exp'] ?? 0) < time()) return null;
        return $data;
    }

    private static function b64u(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64d(string $data): string {
        $pad = str_repeat('=', (4 - strlen($data) % 4) % 4);
        return base64_decode(strtr($data, '-_', '+/') . $pad);
    }
}
