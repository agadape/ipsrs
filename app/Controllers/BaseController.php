<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'text', 'ui', 'rs'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /** Render a page view wrapped in the main layout. */
    protected function render(string $view, array $data = []): string
    {
        $data['content_view'] = $view;
        return view('layout/main', $data);
    }

    /**
     * Validasi input — bila gagal, redirect back dengan error + input lama.
     * Mengembalikan true bila validasi lolos, atau ResponseInterface bila gagal.
     */
    protected function validateOrFail(array $rules, string $errorMsg = ''): true|\CodeIgniter\HTTP\ResponseInterface
    {
        if ($this->validate($rules)) {
            return true;
        }
        $msg = $errorMsg ?: 'Mohon lengkapi seluruh data yang wajib diisi.';
        $errors = [];
        foreach ($this->validator->getErrors() as $field => $error) {
            $label = ucfirst(str_replace(['id_', '_'], ['', ' '], (string) $field));
            $errors[] = str_replace((string) $field, $label, $error);
        }
        $msg .= ' Detail: ' . implode(' ', $errors);
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => $msg]);
        }
        
        return redirect()->back()->withInput()->with('error', $msg);
    }

    /**
     * Ubah exception menjadi pesan yang aman dibaca pengguna.
     * Error database diterjemahkan; pesan aturan bisnis ditampilkan apa adanya.
     */
    protected function friendlyError(\Throwable $e): string
    {
        $message = $e->getMessage();
        $isDatabaseError = $e instanceof \CodeIgniter\Database\Exceptions\DatabaseException
            || $e instanceof \mysqli_sql_exception
            || str_starts_with($message, 'MySQL error');

        if (!$isDatabaseError) {
            return $message;
        }

        $code = (int) $e->getCode();
        if ($code === 0 && preg_match('/\((\d{4})\)/', $message, $m)) {
            $code = (int) $m[1];
        }

        return match ($code) {
            1062       => 'Data yang sama sudah terdaftar. Periksa kembali isian yang harus unik (misalnya email, kode, atau nomor).',
            1451       => 'Data ini masih dipakai oleh data lain sehingga tidak dapat dihapus atau diubah.',
            1452       => 'Data rujukan yang dipilih tidak ditemukan. Muat ulang halaman lalu coba lagi.',
            1406       => 'Isian terlalu panjang. Persingkat teks lalu coba lagi.',
            default    => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi admin.',
        };
    }

    /**
     * Filter POST data — hanya kembalikan field yang ada di $allowed.
     * Hindari mass assignment vulnerability.
     */
    protected function whitelist(array $allowed): array
    {
        $post = $this->request->getPost();
        return array_intersect_key($post, array_flip($allowed));
    }
}

