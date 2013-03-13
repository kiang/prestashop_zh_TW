Speed up the translation of prestashop
=============

When working with the translation of prestashop, I found it's hard to keep the translation up-to-date as the method how the localized strings saved is based on hash code of specified strings. Once there's any small change in the string, you must translate it again, even if it's just a case change of  one amazing letter.

And it takes much time to translate the same string as it's used by different modules/controllers. I don't like to do such boring job again and again. And luckily, I found a better way. Merge all the strings into one gettext file and translate it using poedit( http://www.poedit.net/ ).

You could fork and download the project here:
https://github.com/kiang/prestashop_zh_TW

The folder "english_reference" contains the english strings extracted from 1.5.4RC. If you only want to work with 1.5.3, you could change the reference to "english_reference_153". Or you could try to extract from your current working version(remember to backup...).

### 1. ###
Once you have the project download in your machine, try to open merge.php and extract.php inside the tool directory.

tool/merge.php - Merge all the translation into one po file.
tool/extract.php - Extract translation inside the po file to corresponding files.

[CODE]
$locale = 'tw';
$enReference = 'english_reference';
[/CODE]

Change the $locale to yours.

### 2. ###
Export your existed translation from backend of prestashop, "Localization" -> "Translations" -> "Export a language"

You should get a file called xx.gzip, like tw.gzip. Rename it to tw.tar and extract it. (If you don't have a software to do that, try to find 7zip).

### 3. ###
Put extracted translation into the root of this project, merge it with existed folders.

### 4. ###
Execute the script tool/merge.php in command line or any way you like. You should then get a file inside the tool folder, like tw.po.

### 5. ###
Open the po file using poedit, or any other one you like. You could start to translate it, without worrying about translating the same string again and again.

### 6. ###
Once you finished, or you just want to see current result. Excute the script tool/extract.php. It will extract the results to corresponding files.

### 7. ###
If you want to import the translation from backend, you must only compress 4 folders, "mails, modules, themes, translations", into one tar file, and rename it to xx.gzip, like tw.gzip. Then, wait, there's one bug with manually created archive inside the importing feature:
https://github.com/PrestaShop/PrestaShop/issues/260

Once you fixed the bug, you should be able to update the translation with the file created above.

Just a remind that there's a feature of poedit called 'Translation Memory' ( http://www.opentag.com/tm.htm ). You could make a custom dictionary based on existed translation, even outside prestashop. Then you could let it translate the strings automatically in the following versions. It will be fast if there are only some small changes.

It's a little bit complex, but could save you much time!!

And hope the core team would change to use gettext format in the near future, as it's better than current way. Agree it? Put your comments in this topic:
http://www.prestashop.com/forums/topic/230955-about-changing-the-format-of-translation-to-gettext/
