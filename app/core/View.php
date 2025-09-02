<?php
namespace app\core;

class View
{
    /**
     * Render a template from app/views
     *
     * @param string $template path relative to app/views
     * @param array $vars variables to replace in template
     * @return string
     */
    public static function render(string $template, array $vars = []): string
    {
        $path = __DIR__ . '/../views/' . $template;
        if (!is_file($path)) {
            return '';
        }
        $content = file_get_contents($path);
        foreach ($vars as $key => $value) {
            $value = (string) $value;
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $content = str_replace('#' . $key . '#', $value, $content);
            $content = str_replace('#' . strtoupper($key) . '#', $value, $content);
        }
        return $content;
    }
}
