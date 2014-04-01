��� ����� phpDocumentor ����� phar � �������(template) ��� ����

(��-����������� ����� PEAR-����� � �������� �����-�� �����)
==============================================================

I. ��������� � �������������

1. ������ phar http://phpdoc.org/phpDocumentor.phar

2. ��� ��������� ���� � ��������� ������ �����
> php phpDocumentor.phar -d "path1" -t "path2"
-d : ���������� ������� (������ ������� ����)
-t : ���������� �����

3. ��� �������� � ���������� ������ ��������� ������

> php phpDocumentor.phar -d "path1" -t "path2" --template "checkstyle"

(����� ����� ���������� http://www.phpdoc.org/docs/latest/getting-started/changing-the-look-and-feel.html)
(������ �� ��������� - clean)

II. ��������� ��������

1. ������������� phar-�����
> php -r "$phar = new Phar('phpDocumentor.phar'); $phar->extractTo('./docs');"

(������ http://stackoverflow.com/questions/12997385/extracting-files-from-phar-archive)
2. ������� ��������� � ���������� data\templates

3. ������� ����� ������ ���������� (� ������ �� responsive-twig   wheels-doc)

4. � php.ini ������
phar.readonly = Off

5. ������� ���� c �������� �� ������ 3.