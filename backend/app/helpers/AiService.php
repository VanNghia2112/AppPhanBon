<?php

class AiService
{
    public static string $host = '127.0.0.1';
    public static int $port   = 9000;

    /**
     * Kiểm tra xem cổng 9000 đã mở (AI đang chạy) hay chưa.
     */
    public static function isAlive(): bool
    {
        $fp = @fsockopen(self::$host, self::$port, $errno, $errstr, 0.3);
        if ($fp) {
            fclose($fp);
            return true;
        }
        return false;
    }

    /**
     * Đảm bảo AI server đang chạy.
     * Nếu chưa chạy thì gọi start_ai.bat để khởi động, rồi đợi tối đa $waitSeconds.
     */
    public static function ensureRunning(int $waitSeconds = 8): void
    {
        // Nếu đã chạy thì thôi
        if (self::isAlive()) {
            return;
        }

        // Đường dẫn tới file .bat
        $root = realpath(__DIR__ . '/../../'); // Website-PhanBon
        $bat  = $root . DIRECTORY_SEPARATOR . 'ai' . DIRECTORY_SEPARATOR . 'start_ai.bat';
        if (!is_file($bat)) {
            // Không tìm thấy file khởi động -> bó tay
            return;
        }

        // Lệnh chạy nền, thu nhỏ cửa sổ
        $cmd = 'cmd /c start "" /MIN "' . $bat . '"';

        // Gửi lệnh chạy, không chờ kết quả
        @pclose(@popen($cmd, 'r'));

        // Đợi AI boot lên
        $deadline = microtime(true) + $waitSeconds;
        while (microtime(true) < $deadline) {
            if (self::isAlive()) {
                return;
            }
            usleep(300000); // 0.3 giây
        }
    }
}
