<?php
  header("Content-Type: application/vnd.ms-excel");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("content-disposition: attachment;filename=$nombre.xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo base_url(); ?>js-css/estaticos/css/bootstrap.min.css">
    <title>Document</title>
    <style>
    table tr td {
        border: 1px solid #C1C2C2;
        text-align: center;
    }

    table thead tr {
        font-weight: bold;
    }

    hr {
        color: #C1C2C2;
        padding: 0;
        margin: 0;
    }
    </style>
</head>

<body>
    <div class="">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <!-- <td width='100'>
                        <img src="<?php echo base_url(); ?>/imagenes/LogocucF.png" alt="" width='100'>
                    </td> -->
                    <td colspan='<?php echo $col?>'>
                        <?php echo $titulo?>
                    </td>
                </tr>
                <tr>
                    <td colspan='<?php echo $col?>' style="font-weight: normal;text-align: justify">
                        <?php echo $leyenda ?>
                    </td>
                </tr>
                <tr>
                    <?php
        foreach($datos as $row) {
          echo "<td>No</td>";
          foreach($row as $key => $val)echo "<td>$key</td>";
          break;
        }
        ?>
                </tr>
            </thead>
            <tbody>
                <?php
        $i = 1;
        foreach($datos as $row) {
          echo "<tr>";
          echo "<td>$i</td>";
          foreach($row as $key => $val)  echo "<td>$val</td>";
          echo "</tr>";
          $i++;
        }
      ?>
            </tbody>
        </table>
    </div>
</body>

</html>