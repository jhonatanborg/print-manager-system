<?php

if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['s_login'])) {

  session_destroy();

  header("Location:logout.php"); exit;



}
include_once("../conexao_bd.php");

 $VarID    = $_SESSION['s_id'];

 $VarLogin = $_SESSION['s_login'];

 $VarNome  = $_SESSION['s_nome'];

 $VarNivel = $_SESSION['s_nivel'];

$mes = date ("m");

$ano = date ("Y");

$sql_unidades = "SELECT * FROM unidades";
$result = mysqli_query($conn,$sql_unidades);
$unidades = [];
while ($row = mysqli_fetch_assoc($result)) {
  $unidades[] = $row;
}

// Converter o array em uma string JSON
$unidades_json = json_encode($unidades);
?>







<!DOCTYPE html>

<html lang="pt-br">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Home</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>


<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="#">Fabri Gráfica Digital</a>

            </div>

            <div id="navbar" class="navbar-collapse collapse">

                <ul class="nav navbar-nav">

                    <li><a href="inicio.php">PRODUTOS</a></li>
                    <li class="active"><a href="unidades.php">UNIDADES</a></li>

                    <li><a href="solicitados.php">SOLICITADOS</a></li>
                    <li><a href="unidades.php">SOLICITADOS</a></li>

                    <li><a href="solicitar-servicos.php">SOLICITAR SERVIÇO</a></li>

                    <li><a href="pesquisa.php">RELÁTORIOS</a></li>



                </ul>

                <ul class="nav navbar-nav navbar-right">





                    <li><a href="#"><?php echo "$VarNome"; ?></a></li>

                    <li><a href="../administrador/logout.php">SAIR</a></li>

                </ul>

            </div>
            <!--/.nav-collapse -->

        </div>
        <!--/.container-fluid -->

    </nav>
    <div id="app">

        <div class="container">
            <div>
                <h4>Unidades</h4>
            </div>

            <div class="row">
                <div class="col-sm-6" v-for="(unity, key) in unitys">
                    <div v-if="!unity.isEdit" class="card-unity">
                        <div class="card-header-unity">
                            <div>
                                <span class="card-unity-title" v-text="unity.name"></span>
                            </div>
                            <div class="pointer" @click="handleEditUnity(key)">
                                <i class="material-icons">edit</i>
                            </div>
                        </div>
                        <div class="card-body-unity">
                            <span class="card-body-text">Crédito: <b v-text="unity.value">
                                </b> </span>
                        </div>
                    </div>
                    <div v-if="unity.isEdit" class="card-unity">
                        <div class="card-header-unity">
                            <div>
                                <span class="card-unity-title" v-text="unity.name">ICHS</span>
                            </div>
                            <div @click="unitys[key].isEdit = false">
                                <i class="material-icons">close</i>
                            </div>
                        </div>
                        <div class="card-body-unity">
                            <div class="form-group" :class="{ focused: isInputFocused }">
                                <label class="form-label" for="last">Crédito</label>
                                <input v-model="inputValue" @focus="handleInputFocus" @blur="handleInputBlur" id="last"
                                    class="form-input" type="number" />
                            </div>
                            <div>
                                <button @click="saveForm(unity.id)" class="btn btn-primary">
                                    Confirmar
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <footer>
            <div class="footer" id="footer">
                <div class="container">
                    <p class="pull-left"> Copyright © Vedas Sistemas 2023. Todos os direitos reservados. </p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script>
    var app = new Vue({
        el: '#app',
        data: {
            message: 'Hello Vue!',
            inputValue: '',
            isInputFocused: false,
            unitys: <?php echo json_encode($unidades); ?>,
        },
        async mounted() {
            const unitys = await this.handlindList(this.unitys);
            this.unitys = unitys;
        },
        methods: {
            handleInputFocus() {
                this.isInputFocused = true;
            },
            handleInputBlur() {
                if (this.inputValue === '') {
                    this.isInputFocused = false;
                }
            },
            async handlindList(list) {
                const newUnity = list.map(unity => {
                    return {
                        ...unity,
                        isEdit: false,
                    }
                })
                return newUnity;
            },
            async handleEditUnity(key) {
                this.unitys.map((unity, index) => {
                    if (index === key) {
                        this.unitys[index].isEdit = true;
                    } else {
                        this.unitys[index].isEdit = false;
                    }
                })
                this.inputValue = this.unitys[key].value;
            },
            async saveForm(id) {
                const payload = {
                    value: this.inputValue,
                    id: id,
                }

                fetch('./save_unity.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    })
                    .then(response => response.json())
                    .then(resp => {
                        console.log(resp.data)
                        const newUnitys = resp.data.map(
                            unity => {
                                return {
                                    ...unity,
                                    isEdit: false,
                                }
                            }
                        )
                        this.unitys = newUnitys;
                    })
                    .catch(error => {
                        // Tratamento de erro
                        console.error(error);
                    });

            }
        }
    })
    </script>
</body>

<style>
body {
    background-color: #f5f5f5;
}

.pointer {
    cursor: pointer;
}

.card-unity {
    width: 100%;
    height: 100%;
    background-color: #fff;
    border-radius: 5px;
    margin-bottom: 20px;
    padding: 10px 15px;
}

.card-header-unity {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.card-unity-title {
    font-size: 1em;
    font-weight: 600;
    color: #333;
    margin-bottom: 0;
}

.form-group {
    position: relative;

    &+.form-group {
        margin-top: 30px;
    }
}

.form-label {
    position: absolute;
    left: 0;
    top: 10px;
    color: #999;
    background-color: #fff;
    z-index: 10;
    transition: transform 150ms ease-out, font-size 150ms ease-out;
}

.focused .form-label {
    transform: translateY(-125%);
    font-size: .75em;
}

.form-input {
    position: relative;
    padding: 12px 0px 5px 0;
    width: 100%;
    outline: 0;
    border: 0;
    box-shadow: 0 1px 0 0 #e5e5e5;
    transition: box-shadow 150ms ease-out;

    &:focus {
        box-shadow: 0 2px 0 0 blue;
    }

    &::-webkit-outer-spin-button,
    &::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    &[type=number] {
        -moz-appearance: textfield;
    }

    &+.form-label {
        pointer-events: none;
    }



}

.form-input.filled {
    box-shadow: 0 2px 0 0 lightgreen;
}
</style>

</html>