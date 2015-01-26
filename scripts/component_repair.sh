#!/bin/sh

bx_standart_repair(){
    cp  -rn ~/phpStorm_bitrix_paramsGenerator/data/bx-components/mscoder/standard.elements ./
}


bx_component_repair(){
    if echo $2 | grep -E '\.list$' > /dev/null
    then
        echo "LIST"
    else
        if echo $2 | grep -E '\.' > /dev/null
        then
            echo "CHILD OF COMPLEX"
        else
            echo "COMPLEX"
        fi
    fi
    # @todo: формируем .description
}