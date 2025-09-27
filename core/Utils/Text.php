<?php

namespace PHPFramework\Utils;

class Text {
    public static function slugify(string $text, ?int $id = null) : string
    {
        $text = mb_strtolower($text, 'UTF-8');

        if (preg_match('/[а-яё]/u', $text)) {
            $cyr = [
                'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
                'е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i',
                'й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n',
                'о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t',
                'у'=>'u','ф'=>'f','х'=>'h','ц'=>'ts','ч'=>'ch',
                'ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'',
                'э'=>'e','ю'=>'yu','я'=>'ya'
            ];
            $text = strtr($text, $cyr);
        } else {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        }
 
        $text = preg_replace('/[^a-z0-9- ]+/', '', $text);
        $text = preg_replace('/[^a-z0-9-]+/', '-', $text);
        $text = trim($text, '-');

        if ($text && $id !== null) {
            $text .= "-{$id}";
        }

        return $text ?: '';
    }
}