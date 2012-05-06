Причина появления - см. [opencartforum.ru: Репозиторий на Github. Будет ли?](http://opencartforum.ru/topic/7461-%d1%80%d0%b5%d0%bf%d0%be%d0%b7%d0%b8%d1%82%d0%be%d1%80%d0%b8%d0%b9-%d0%bd%d0%b0-github-%d0%b1%d1%83%d0%b4%d0%b5%d1%82-%d0%bb%d0%b8/page__fromsearch__1)

`master` branch - копия основного SVN-репозитория ocStore http://www.assembla.com/code/ocstoreru/subversion/nodes
Будет обновляться автоматически оттуда. Из `master` изменения будут попадать в `dev` ветку.

`dev` -- git-версия репозитория, основная. Оставил только 1.5.1.3.
Если здесь будет вестись работа над более старыми версиями - их всегда можно взять из `master` и расположить в отдельной ветке для продолжения работы.


## Полезные материалы для начинающих знакомство с Git

* [Git - SVN Crash Course](http://git.or.cz/course/svn.html) — для тех, кто мигрирует с SVN на Git; таблицы соответствия для быстрого старта и укладывания в голове аналогий
* http://progit.org/book/ru/ — хороший перевод об устройстве и логике работы Git; кратко, последовательно и понятно, без "воды" и лирических отступлений
* http://habrahabr.ru/blogs/development/68341/ — одна из самых понятных и кратких статей о правильной организации коллективной работы над проектами с использованием Git
* [Git-SVN Comparison](https://git.wiki.kernel.org/index.php/GitSvnComparsion)

В продолжение статьи на Хабре о методах коллективной работы с применением Git - наиболее полная и широко известная модель ведения проектов с использованием Git:

http://nvie.com/posts/a-successful-git-branching-model/

### См. также:

* [Первичные настройки Git](http://rb.labtodo.com/page/git-initial-settings)
* [Вывод Git branch в подсказку командной строки](http://rb.labtodo.com/page/git-branch-in-bash-prompt) (Linux, bash)
