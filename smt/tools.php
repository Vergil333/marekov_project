<?php include 'include/main.php'; ?>
<?php
try {
  $conn = pdo_connect_mysql();
  $stmt = $conn->prepare("SELECT id, name FROM customer;");
  $stmtDev = $conn->prepare("SELECT id, ip FROM device;");
  $stmt->execute();
  $stmtDev->execute();
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $dataDev = $stmtDev->fetchAll(PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
  echo $e->getMessage();
}
?>

<?php template_header('Tools') ?>

    <main>
      <h3>Spravovať zákazníkov</h3>
      <hr>
      <h4>Pridať zákazníka</h4>
      <form id="cstmrAdd">
        <label for="cstmrName">Názov:</label>
        <input type="text" id="cstmrName" name="customer_name">
        <label for="cstmrAct">Meraný:</label>
        <input type="checkbox" id="cstmrAct" name="customer_active">
        <input type="submit" value="Pridať">
      </form>
      <h4>Odstrániť zákazníka</h4>
      <form id="cstmrDel">
        <label for="cstmrSlctId">Zákazník:</label>
        <select id="cstmrSlctId" name="customer_select_id">
<?php foreach ($data as $row): ?>
          <option value=<?=$row["id"]?>><?=$row["name"] ?></option>
<?php endforeach; ?>
        </select>
        <input type="submit" value="Odstrániť">
      </form>
      <hr>
      <h3>Spravovať zariadenia</h3>
      <hr>
      <h4>Pridať zariadenie</h4>
      <form id="devAdd">
        <label for="devName">Názov:</label><input type="text" id="devName" name="device_name">
        <label for="devIP">IP:</label><input type="text" id="devIP" name="device_ip">
        <label for="devAct">Aktívne:</label><input type="checkbox" id="devAct" name="device_active">
        <input type="submit" value="Pridať">
      </form>
      <h4>Odstrániť zariadenie</h4>
      <form id="devDel">
        <label for="devSlct">Zariadenie:</label>
        <select id="devSlct" name="device_select_id">
<?php foreach ($dataDev as $row): ?>
          <option value=<?=$row["id"]?>><?=$row["ip"] ?></option>
<?php endforeach; ?>
        </select>
        <input type="submit" value="Odstrániť">
      </form>
    </main>
<?php template_footer() ?>
