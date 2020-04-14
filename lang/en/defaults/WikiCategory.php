<?php
printf('=====%s=====', T_('How To Use Categories'));
printf(T_('This wiki is using a very flexible but simple categorizing system to keep everything properly organized.'));
printf('--- ---');
printf('===1. %s===', T_('Adding a page to an existing category'));
printf(T_('To \'\'add a page to an existing category\'\' simply add a link to the relevant category page. For example, to mark page %s as a child of category %s, just add a link to %s from %s. This will automatically add %s to the list of pages belonging to that category. Category links are put by convention at the end of the page, but the position of these links does not affect their behavior.'), '##""MyPage""##', '##""MyCategory""##', '##""MyCategory""##', '##""MyPage""##', '##""MyPage""##');
printf('--- ---');
printf('===2. %s===', T_('Adding a subcategory to an existing category'));
printf(T_('To \'\'create a hierarchy of categories\'\', you can follow the same instructions to add pages to categories. For example, to mark category %s as a child (or subcategory) of another category %s just add a link to %s in %s. This will automatically add %s to the list of %s\'s children.'), '##""Category2""##', '##""Category1""##', '##""Category1""##', '##""Category2""##', '##""Category2""##', '##""Category1""##');
printf('--- ---');
printf('===3. %s===', T_('Creating new categories'));
printf(T_('To \'\'start a new category\'\' just create a page containing %s. This will mark the page as a special %s and will output a list of pages belonging to the category. Category page names start by convention with the word %s but you can also create categories without following this convention. To add a new category to the master list of categories just add a link from it to %s.'), '##""{{category}}""##', '//category//', '##Category##', 'CategoryCategory');
printf('--- ---');
printf('===4. %s===', T_('Browsing categories'));
printf(T_('To \'\'browse the categories\'\' available on your wiki you can start from %s. If all pages and subcategories are properly linked as described above, you will be able to browse the whole hierarchy of categories starting from this page.'), 'CategoryCategory');
printf('--- ----');
printf('CategoryWiki');
?>
