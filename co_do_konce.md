# Co jeste udelame z technickeho hlediska #
  * Rozdeleni na quarters a KPIs
    * nova tabulka detail\_tracking s vazbami na quarter a kpi.
    * nove stranky a tridy detail\_planning a detail\_tracking.
  * u KPI sloupec LC (kteremu LC patri - pozor muze patrit i 2 atd. takze asi spis nejaka dalsi tabulka, aby mohly jednotlive LC mit vlastni)
    * vazebni tabulka LC\_KPI - to be done
  * u KPI sloupec ktery urcuje jednotku (%, cislo, days)
    * pridat sloupec
  * u KPI sloupec ktery urcuje importance
    * pridat sloupec
  * u KPI sloupec ktery urcuje jestli hodnota termu je soucet quarters nebo jestli se rovna poslednimu quarteru
    * pridat sloupec
  * implementaci toho vyse u planingu a reportu
    * viz detail\_planning.php
    * TODO predelat stejne tak entering na detail\_entering.
  * selekty podle quarters, terms, KPIs, Areas, LC
    * viz trida DetailTracking
  * eventualne export celeho LC do Excelu
    * ??? dopsat do stranky ExportDoExcelu
  * remindery
    * TODO Krystof
  * event. admin interface
    * co by mel AdminInterface obsahovat? Dopsat do odkazane stranky.
  * zamykani formularu k datum
    * TODO Krystof
    * vyzaduje automaticke generovani termu a quarteru podle pravidel od Vojty
    * zacatek a konec vzdy pred konferenci (15.7. ???)

  * na mne jsou reporty - jedna zalozka jako je zde - jen z KPIs co poslal vojta http://www.corporater.com/en/products/popup/features/scorecard/bsc-classical.html
  * druha budou grafy ktere budou chtit a pod nimy vypis v tabulce