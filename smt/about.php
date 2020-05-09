<?php include 'include/main.php'; ?>
<?php template_header('About') ?>

    <main>
      <h2>TODO list</h2>
      <ul>
        <li>Pridať vyťaženosť SDS jednotlivých vstupov na hlavnej stránke. POWER TRESHOLD indikátor na jednotlivých optočlenoch</li>
        <li>Spraviť vykresľovanie grafov ako sú vyťažené fázy za posledné 2 mesiace</li>
        <li>Načítať stránku a cez ajax len volať php scripty, ktoré budú vykresľovať obsah. Takto docielime efekt automatického updatu stránky</li>
        <li>POZOR!!! Opraviť kritickú zraniteľnosť v include/functions.php pri spúšťaní exec príkazu! Možnosť spúšťať príkazy!!!!</li>
        <li>Keď v nástrojoch kliknem pridať/zmazať zákazníka, tak na pravo bude float box, kde sa tieto akcie budú vypisovať.</li>
        <li>Potvrdzovacie okno pri mazaní device/customer</li>
        <li>Možnosť vynulovania počítadla na SDS odoslaním snmp príkazu</li>
        <li>Keď zobrazím SDS, tak ukáže pre ktorých zákazníkov je daná SDS priradená</li>
        <li>Po kliknutí na device sa zobrazí aj tabuľka, ktorý zákazníci používajú dané zariadenie</li>
      </ul>
      <h2>Ako nainštalovať stránku</h2>
      <p>TBA</p>
      <h2>INFO</h2>
      <dl>
        <dt>Ako funguje stránka?</dt>
        <dd>Stránka bude bez problémov fungovať na LAMP/apache service ale aj na nginx</dd>
        <dd>Skladá sa primárne zo skriptu, ktorý spúšťa cronjob v linuxe a ten updatuje databázu. Stránka slúži len na jednoduchý manažment a rýchly prehľad nameraných hodnôt.</dd>
        <dd>Pozostáva z nasledujúcich základných častí:</dd>
        <dd>Hlavná stránka (prehľad SDS zariadení)</dd>
        <dd>Správa SDS zariadenia</dd>
        <dd>Manažment SDS zariadení (pridávanie/odstránenie)</dd>
        <dd>Manažment zákazníkov (pridávanie/odstránenie)</dd>
        <dd>Report spotreby</dd>

        <dt>Aké jazyky a pluginy sú použité na stránke?</dt>
        <dd>Použité sú html, css, php, javascript, jquery plugin, ajax.</dd>
        <dd>Niektoré veci sa nedali spraviť bez pridania jquery.</dd>
        <dd>Kôli jednoduchosti a rýchlosti stránky sa vynechali webframeworky. V budúcnosti sa kľudne môžu použiť.</dd>

        <dt>Prečo sú niektoré php funkcie v osobytných súboroch?</dt>
        <dd>Nie je možné ich zakomponovať do jedného php súboru, pretože plnia len jednu špecifickú úlohu a slúžia na update stránky ale len špecifického úseku.</dd>
        <dd>HTML ani PHP nedokážu zavolať php funkciu po stlačení tlačidla bez toho, aby sa stránka nerefreshla.</dd>
        <dd>Preto sa loaduje jquery a použije sa ajax funkcia na volanie php stránky, ktorá vykoná konkrétny príkaz. Táto zmena sa prepíše len vopred určenom div tag.</dd>

        <dt>Ako funguje skript na čítanie údajov z SDS?</dt>
        <dd>Definujú sa parametre pre prihlásenie do databázy</dd>
        <dd>Prečítame z databázy zariadenia</dd>
        <dd>Ak sa nepripojíme do databázy, vráti nám to ERROR alebo prázdnu hodnotu, ktorú nehľadáme a ak je toto prázdne, znamená to, že pripojenie zlyhalo a skript sa vypína</dd>
        <dd>Pre každý výsledok načítaný z tabuľky device skúšame čítať odpoveď z SDS pomocou curl príkazu</dd>
        <dd>Hodnoty zapisujeme do premenných a ak je nejaká hodnota prázdna, znamená to že pripojenie zlyhalo (SDS neodpovedá) a daný krok preskakujeme.</dd>
        <dd>Updatujeme tabuľku nameraných hodnôt, databáza prikladá dátum zápisu a aktualizuje aj tabuľku zariadení s aktuálnymi hodnotami.</dd>

        <dt>Aká databáza sa používa?</dt>
        <dd>Databáza používa set mysql príkazov, teda môže byť použitá propretárna databáza mysql alebo opensource mariadb. Fungovať bude na oboch bez problémov.</dd>
        <dd>Na správu databázy dporúčam easy opensource tool adminer. Je to len jeden file.</dd>

        <dt>Čo všetko je potrebné spraviť na rozbehanie stránky?</dt>
        <dd>Importovať databázu</dd>
        <dd>Nahrať súbory stránky do /var/www/html alebo iného priečinka v závislosti od pužitého OS alebo inštalovaného web service</dd>
        <dd>Nahrať skript a nastaviť cronjob na spúšťanie v linuxe. Odporúčam 5 minútový interval. V prípade, že pre každú curl odpoveď sa bude čakať 1 sekundu a zariadení bude napríklad 240, tak sú to 4 minúty čakacej doby. Nechceme spúšťať skript 2-krát pred tým, než predchádzajúci dokončí úlohu.</dd>
        <dd>PHP konfiguračný súbor php.ini povoliť pdo_mysql extension!</dd>

        <dt>Načo je použitá snmpwalk funkcia v php?</dt>
        <dd>Slúži nám na zistenie všetkých údajov z SDS. Nie je použitá funkcia snmpwalk cez PHP, pretože to nefungovalo. Takže php vyvolá funkciu shell_exec, spustí lokálne príkaz snmpwalk, uloží do premennej a vypíše na stránke.</dd>

        <dt>Prečo je všade použité _GET a nie _POST? Nie je _POST bezpečnejšie?</dt>
        <dd>Aby sa prehliadač nepýtal pri refresh stránky či znova odoslať data. POST je bezpečnejší ale v tomto prípade je to jedno, nerobíme registráciu užívateľa, kde by bolo viditeľné heslo. V prípade núdze sa to dá ľahko prerobiť na POST.</dd>
      </dl>
      <h1>PROBLÉMY S DATABÁZOU? <a href="adminer-4.7.6.php">KLIKNI TU!</a></h1>
    </main>
<?php template_footer() ?>
