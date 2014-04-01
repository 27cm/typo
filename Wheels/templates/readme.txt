Как юзать phpDocumentor через phar и шаблоны(template) для него

(по-нормальному через PEAR-канал в виндовсе какая-то хрень)
==============================================================

I. УСТАНОВКА И ИСПОЛЬЗОВАНИЕ

1. Качаем phar http://phpdoc.org/phpDocumentor.phar

2. Для генерации доки в командной строке пишем
> php phpDocumentor.phar -d "path1" -t "path2"
-d : директория проекта (откуда генерим доки)
-t : директория доков

3. Для шаблонов к предыдущей строке указываем шаблон

> php phpDocumentor.phar -d "path1" -t "path2" --template "checkstyle"

(можно здесь посмотреть http://www.phpdoc.org/docs/latest/getting-started/changing-the-look-and-feel.html)
(шаблон по умолчанию - clean)

II. ИЗМЕНЕНИЕ ШАБЛОНОВ

1. Распаковываем phar-архив
> php -r "$phar = new Phar('phpDocumentor.phar'); $phar->extractTo('./docs');"

(отсюда http://stackoverflow.com/questions/12997385/extracting-files-from-phar-archive)
2. Шаблоны находятся в директории data\templates

3. Создаем новый шаблон копипастом (я сделал из responsive-twig   wheels-doc)

4. В php.ini ставим
phar.readonly = Off

5. Генерим доку c шаблоном по пункту 3.