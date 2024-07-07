<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];

    function obfuscate($code) {
        
        $variables = [];
        preg_match_all('/\b(bool|byte|sbyte|char|decimal|double|float|int|uint|long|ulong|short|ushort|string|object|byte\[\]|sbyte\[\]|char\[\]|decimal\[\]|double\[\]|float\[\]|int\[\]|uint\[\]|long\[\]|ulong\[\]|short\[\]|ushort\[\]|string\[\]|object\[\])\s+\$?(\w+)\b/', $code, $matches);
        foreach ($matches[2] as $varName) {
            $variables[$varName] = 'var' . substr(md5($varName), 0, 8);
        }
        $code = str_replace(array_keys($variables), array_values($variables), $code);

        
        $functions = [];
        preg_match_all('/\b(static\s+|public\s+|private\s+|protected\s+)?(void|int|bool|char|decimal|double|float|string|object|byte\[\]|sbyte\[\]|char\[\]|decimal\[\]|double\[\]|float\[\]|int\[\]|uint\[\]|long\[\]|ulong\[\]|short\[\]|ushort\[\]|string\[\]|object\[\])?\s+(\w+)\s*\(/', $code, $matches);
        foreach ($matches[3] as $funcName) {
            if ($funcName !== 'Main') {
                $functions[$funcName] = 'func' . substr(md5($funcName), 0, 8);
            }
        }
        $code = str_replace(array_keys($functions), array_values($functions), $code);

        
        $code = preg_replace_callback('/"([^"]*)"|\'([^\']*)\'/', function ($matches) {
            if (isset($matches[1])) {
                
                return 'System.Text.Encoding.UTF8.GetString(Convert.FromBase64String("' . base64_encode($matches[1]) . '"))';
            } else {
                
                return 'System.Text.Encoding.UTF8.GetString(Convert.FromBase64String("' . base64_encode($matches[2]) . '"))[0]';
            }
        }, $code);

        return $code;
    }

    $obfuscatedCode = obfuscate($code);
    echo $obfuscatedCode;
}
?>
