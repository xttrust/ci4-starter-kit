<?php namespace App\Libraries;

final class Theme
{
    public function backend(string $view, array $data = []): string
    {
        $data['title'] = $data['title'] ?? 'Admin';
        return view($view, $data, ['saveData' => true]);
    }

    public function frontend(string $view, array $data = []): string
    {
        $data['title'] = $data['title'] ?? 'Site';
        return view($view, $data, ['saveData' => true]);
    }
}
