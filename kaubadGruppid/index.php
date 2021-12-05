<?php
require("conf.php");
session_start();
/*if (!isset($_SESSION['tuvastamine'])) {
    header('Location: login.php');
    exit();
}*/

require("functions.php");
$sort = "kaubanimi";
$search_term = "";
if(isset($_REQUEST["sort"])) {
    $sort = $_REQUEST["sort"];
}
$sortgrupp = "kaubagrupp";
if(isset($_REQUEST["sortgrupp"])) {
    $sortgrupp = $_REQUEST["sortgrupp"];
}
if(isset($_REQUEST["search_term"])) {
    $search_term = $_REQUEST["search_term"];
}
if(isset($_REQUEST["lisa_kaup"])) {
    if(!empty(trim($_REQUEST["kaubanimi"])) && !empty(trim($_REQUEST["hind"]))) {
        lisaKaup($_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
        header("Location: index.php");
        exit();
    }
}
if(isset($_REQUEST["lisa_kaubagrupp"]) && isAdmin()) {
    if(!empty(trim($_REQUEST["kaubagrupp"]))) {
        if (kontroll(trim($_REQUEST["kaubagrupp"])) == 0) {
            lisaKaubagrupp($_REQUEST["kaubagrupp"]);
            header("Location: index.php");
            exit();
        }
    }
}
if(isset($_REQUEST["kustutaKaup"])) {
    kustutaKaup($_REQUEST["kustutaKaup"]);
}
if(isset($_REQUEST["kustutaKaubagrupp"])) {
    kustutaKaubagrupp($_REQUEST["kustutaKaubagrupp"]);
}
if(isset($_REQUEST["save"])) {
    muudaKaup($_REQUEST["changed_id"], $_REQUEST["kaubanimi"], $_REQUEST["hind"], $_REQUEST["kaubagrupp_id"]);
}
$kaubad = kaubadData($sort, $search_term);
$kaubagrupp = kaubagruppData($sortgrupp);
?>
    <!DOCTYPE html>
    <html lang="et">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="styles.css">
        <?php if(($_SESSION["onAdmin"])==0):?>
            <title>Kaubad ja gruppid</title>
        <?php endif; ?>
        <?php if(($_SESSION["onAdmin"])==1):?>
            <title>Kaubagruppid</title>
        <?php endif; ?>
    </head>
    <body>
    <header class="header">
        <h2 class="logged_in_user"> <?=$_SESSION["kasutajad"]?> on sisse logitud</h2>
        <form action="logout.php" method="post">
            <input class="button_submit" type="submit" value="Logi välja" name="logout">
        </form>
        <div class="container">
            <?php if(($_SESSION["onAdmin"])==0):?>
                <h1 class="table_lable">Tabel | Kaubad ja gruppid</h1>
            <?php endif; ?>
            <?php if(($_SESSION["onAdmin"])==1):?>
                <h1 class="table_lable">Tabel | Kaubagruppid</h1>
            <?php endif; ?>
        </div>
    </header>
    <main class="main">
        <?php if(($_SESSION["onAdmin"])==0):?>
        <div class="container">
            <form action="index.php">
                <input class="form__input" type="text" name="search_term" placeholder="Otsing...">
            </form>
        </div>
        <?php if(isset($_REQUEST["edit"])): ?>
            <?php foreach($kaubad as $kaup): ?>
                <?php if($kaup->id == intval($_REQUEST["edit"])): ?>
                    <div class="container">
                        <form action="index.php">
                            <input type="hidden" name="changed_id" value="<?=$kaup->id ?>"/>
                            <input class="form__input" type="text" name="kaubanimi" value="<?=$kaup->kaubanimi?>">
                            <input class="form__input" type="text" name="hind" value="<?=$kaup->hind?>">
                            <?php echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id"); ?>
                            <a title="Katkesta muutmine" class="cancelBtn" href="index.php" name="cancel">X</a>
                            <input class="button_submit" type="submit" name="save" value="&#10004;">
                        </form>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php endif; ?>
        <div class="container">
            <table>
                <?php if(($_SESSION["onAdmin"])==0):?>
                <thead>
                <tr>
                    <th>Id</th>
                    <th><a href="index.php?sort=kaubanimi">Kaubanimi</a></th>
                    <th><a href="index.php?sort=hind">Hind</a></th>
                    <th><a href="index.php?sort=kaubagrupp">Kaubagrupp</a></th>
                    <th></th>
                </tr>
                </thead>
                <?php endif;?>
                <?php if(($_SESSION["onAdmin"])==1):?>
                <thead>
                <tr>
                    <th>Id</th>
                    <th><a href="index.php?sortgrupp=kaubagrupp">Kaubagrupp</a></th>
                    <th></th>
                </tr>
                </thead>
                <?php endif;?>
                <tbody>
                <?php foreach($kaubad as $kaup): ?>
                    <?php if(($_SESSION["onAdmin"])==0):?>
                    <tr>
                        <td><strong><?=$kaup->id ?></strong></td>
                        <td><?=$kaup->kaubanimi ?></td>
                        <td><?=$kaup->hind ?></td>
                        <td><?=$kaup->kaubagrupp ?></td>
                        <td>
                            <a title="Muuda kaup" class="editBtn" href="index.php?edit=<?=$kaup->id?>">&#9998;</a>
                            <a title="Kustuta kaup" class="deleteBtn" href="index.php?kustutaKaup=<?=$kaup->id?>"
                               onclick="return confirm('Oled kindel, et soovid kustutada?');">X</a>
                        </td>
                    </tr>
                    <?php endif;?>
                <?php endforeach; ?>
                <?php foreach($kaubagrupp as $grupp): ?>
                    <?php if(($_SESSION["onAdmin"])==1):?>
                    <tr>
                        <td><strong><?=$grupp->id ?></strong></td>
                        <td><?=$grupp->kaubagrupp ?></td>
                        <td>
                            <a title="Kustuta kaup" class="deleteBtn" href="index.php?kustutaKaubagrupp=<?=$grupp->id?>"
                               onclick="return confirm('Oled kindel, et soovid kustutada?');">X</a>
                        </td>
                    </tr>
                    <?php endif;?>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php if(($_SESSION["onAdmin"])==1):?>
                <form action="index.php">
                    <h2>Kaubagruppi lisamine:</h2>
                    <dl>
                        <dt>Kaubagruppi nimi:</dt>
                        <dd><input class="form__input" type="text" name="kaubagrupp" placeholder="Sisesta nimi..."></dd>
                        <input class="bs" type="submit" name="lisa_kaubagrupp" value="Lisa kaubagrupp">
                    </dl>
                    <?php
                    if(isset($_REQUEST["lisa_kaubagrupp"])) {
                        if (kontroll(trim($_REQUEST["kaubagrupp"])) > 0) {
                            echo "Antud kaubagrupp on olemas";
                        }
                    }
                    ?>
                </form>
            <?php endif;?>
            <?php if(($_SESSION["onAdmin"])==0):?>
                <form action="index.php">
                    <h2>Kauba lisamine:</h2>
                    <dl>
                        <dt>Kaubanimi:</dt>
                        <dd><input class="form__input" type="text" name="kaubanimi" placeholder="Sisesta kaubanimi..."></dd>
                        <dt>Hind:</dt>
                        <dd><input class="form__input" type="text" name="hind" placeholder="Sisesta hind..."></dd>
                        <dt>Kaubagrupp</dt>
                        <dd><?php
                            echo createSelect("SELECT id, kaubagrupp FROM kaubagrupid", "kaubagrupp_id");
                            ?></dd>
                        <input class="bs" type="submit" name="lisa_kaup" value="Lisa kaup">
                    </dl>
                </form>
            <?php endif;?>
        </div>
    </main>
    </body>
    </html>
<?php
/*
 * CREATE TABLE kaubagrupid(
            id int PRIMARY KEY AUTO_INCREMENT,
            kaubagrupp varchar(100)
        );
          CREATE TABLE kaubad(
            id int PRIMARY KEY AUTO_INCREMENT,
            kaubanimi varchar(100),
            hind int,
            kaubagrupp_id int,
            FOREIGN KEY (kaubagrupp_id) REFERENCES kaubagrupid(id)
        );
        INSERT INTO kaubagrupid(kaubagrupp) VALUES ('mänguasjad');
        INSERT INTO kaubad(kaubanimi, hind, kaubagrupp_id) VALUES ('auto', 25, 1);
 */
?>