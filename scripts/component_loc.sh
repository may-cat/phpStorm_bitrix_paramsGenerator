#!/bin/sh

#
# Вычленение локализации
bx_localize_parse(){
    echo "LOCALIZE"
    /z/usr/bin/php.exe -q ~/phpStorm_bitrix_paramsGenerator/scripts/extract.php `pwd`
}
