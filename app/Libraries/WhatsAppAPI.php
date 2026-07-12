<?php
namespace App\Libraries;

class WhatsAppAPI
{
    /**
     * TOKEN API WhatsApp Anda.
     * Jika Anda menggunakan Fonnte, dapatkan token di menu Device (https://md.fonnte.com/device.php).
     */
    private string $token = 'TOKEN_DARI_USER'; 

    /**
     * Target penerima default (bisa ID Grup atau nomor HP).
     * Contoh: '1203630XXXXXXX-group' atau '08123456789'
     */
    private string $defaultTarget = 'NOMOR_ATAU_GRUP_TARGET';

    /**
     * Kirim pesan WhatsApp (Broadcast)
     * 
     * @param string $message Pesan yang ingin dikirim
     * @param string|null $target Target spesifik (opsional)
     * @return bool|string Response dari server API, atau false jika gagal
     */
    public function sendBroadcast(string $message, ?string $target = null)
    {
        $finalTarget = $target ?? $this->defaultTarget;

        if ($this->token === 'TOKEN_DARI_USER' || $finalTarget === 'NOMOR_ATAU_GRUP_TARGET') {
            log_message('error', 'WhatsAppAPI: Token atau Target belum diatur. Pesan batal dikirim.');
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.fonnte.com/send',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
              'target' => $finalTarget,
              'message' => $message,
              'delay' => '2'
          ),
          CURLOPT_HTTPHEADER => array(
            "Authorization: $this->token"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            log_message('error', 'WhatsAppAPI Error: ' . $err);
            return false;
        }

        log_message('info', 'WhatsAppAPI Success: ' . $response);
        return $response;
    }
}
