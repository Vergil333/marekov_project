<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT * FROM customer WHERE id=?;");
  $stmtSDS = $conn->prepare("SELECT device.id, device.ip, customer_input.opt1, customer_input.opt2, customer_input.opt3, device.s0_1_kwh, device.s0_2_kwh, device.s0_3_kwh FROM customer_input INNER JOIN device ON customer_input.device_id=device.id WHERE customer_input.customer_id=?;");
  $stmtTotal = $conn->prepare("SELECT SUM(CASE customer_input.opt1 WHEN 1 THEN device.s0_1_kwh ELSE 0 END) + SUM(CASE customer_input.opt2 WHEN 1 THEN device.s0_2_kwh ELSE 0 END) + SUM(CASE customer_input.opt3 WHEN 1 THEN device.s0_3_kwh ELSE 0 END) AS total_kwh FROM device INNER JOIN customer_input ON device.id=customer_input.device_id WHERE customer_input.customer_id=?;");
  $stmt->execute([htmlspecialchars($_GET["id"])]);
  $stmtSDS->execute([htmlspecialchars($_GET["id"])]);
  $stmtTotal->execute([htmlspecialchars($_GET["id"])]);
  $data = $stmt->fetch(PDO::FETCH_ASSOC);
  $dataSDS = $stmtSDS->fetchAll(PDO::FETCH_ASSOC);
  $dataTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Customer') ?>

    <main>
      <h3 style="text-align:left;">Zákazník ID: <?=$data["id"]; ?> <span style="float:right;">Názov: <?=$data["name"]; ?></span></h3>
      <hr>
      <h4 style="margin-bottom: 0">Informácie</h4>

      <form id="cstmrUpdate">
        <table style="text-align: left">
          <tr>
            <th>ID</th>
            <td><input type="hidden" id="cstmrId" name="customer_id" value="<?=$data['id']; ?>"><?=$data["id"] ?></td>
          </tr>
          <tr>
            <th>Názov</th>
            <td><input type="text" id="cstmrName" name="customer_name" value="<?=$data['name']; ?>"></td>
          </tr>
          <tr>
            <th>Monitorovaný</th>
            <td><input type="checkbox" id="cstmrAct" name="customer_measured" <?php if ($data['measured']) echo "checked"; ?>></td>
          </tr>
        </table>
        <input type="submit" value="Upraviť">
      </form>

      <hr>

      <h4 style="margin-bottom: 0">Priradené zariadenia</h4>

      <div id="cstmrInputContent">
        <p>Optočlen označený ako &#x274c; nie je priradený tomuto zákazníkovi.</p>
        <table style="text-align: center">
            <th>Zariadenie</th>
            <th>Optočlen 1</th>
            <th>Optočlen 2</th>
            <th>Optočlen 3</th>
          </tr>
<?php foreach ($dataSDS as $key=>$value): ?>
          <tr>
            <td><?php echo '<a href="device.php?id='.$value["id"].'">'.$value["ip"].'</a>'; ?></td>
            <td><?php echo ($value["opt1"]) ? $value["s0_1_kwh"] : utf8_encode("&#x274c;"); ?></td>
            <td><?php echo ($value["opt2"]) ? $value["s0_2_kwh"] : utf8_encode("&#x274c;"); ?></td>
            <td><?php echo ($value["opt3"]) ? $value["s0_3_kwh"] : utf8_encode("&#x274c;"); ?></td>
          </tr>
<?php endforeach; ?>
          <tr>
            <th>Spolu</th>
            <td colspan="3"><?=$dataTotal["total_kwh"]; ?> kWh</td>
          </tr>
        </table>
        <button id="cstmrInputEditBtn" value="<?=$data["id"] ?>">Upraviť</button>
      </div>

      <hr>

      <h4>Spotreba mesačne</h4>

      <hr>

      <h4>Grafy</h4>

      <button style="float:right;" id="cstmrDelBtn" value="<?=$data["id"] ?>">Vymazať zákazníka</button>
    </main>
<?php template_footer() ?>
