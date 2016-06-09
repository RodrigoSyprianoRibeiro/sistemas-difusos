jQuery(function($) {

  $('#fuelux-wizard-container')
  .ace_wizard({
    //step: 2 //optional argument. wizard will jump to step "2" at first
    //buttons: '.wizard-actions:eq(0)'
  })
  .on('actionclicked.fu.wizard' , function(e, info){
    $('html, body').animate({ scrollTop: $("body").offset().top }, 'slow');
    if(info.step === 1) {
      if (validarVariaveis() === false) {
        e.preventDefault();
        $(".carregando").addClass('hide');
      } else {
        carregarTermos();
      }
    }
    if(info.step === 2 && info.direction === 'next') {
      if (validarTermos() === false) {
        e.preventDefault();
        $(".carregando").addClass('hide');
      } else {
        carregarRegras();
      }
    }
    if(info.step === 3 && info.direction === 'next') {
      if (validarRegra() === false) {
        e.preventDefault();
        $(".carregando").addClass('hide');
      } else {
        carregarSimulacao();
      }
    }
  })
  .on('finished.fu.wizard', function(e, info){
    inferir();
  }).on('stepclick.fu.wizard', function(e){
    //e.preventDefault();//this will prevent clicking and selecting steps
  });

  $('#modal-wizard-container').ace_wizard();
  $('#modal-wizard .wizard-actions .btn[data-dismiss=modal]').removeAttr('disabled');

  carregarVariaveis();

  function carregarVariaveis() {
    $('#variaveis').load(BASEURL + 'variavel/carregar/id/' + $('#id_projeto').val());
  }

  function carregarTermos() {
    $('#termos').load(BASEURL + 'termo/carregar/id/' + $('#id_projeto').val());
  }

  function carregarRegras() {
    $('#regras').load(BASEURL + 'regra/carregar/id/' + $('#id_projeto').val());
  }

  function carregarSimulacao() {
    $('#simular').load(BASEURL + 'projeto/simular/id/' + $('#id_projeto').val());
  }

  // Nova Variável /////////////////////////////////////////////////////////////
  $(document).on('click', '.nova-variavel', function(e){
    e.preventDefault();
    bootbox.dialog({
      title: "<h4 class='blue bigger'>Nova Variável</h4>",
      message: $('<div>Carregando...</div>').load(BASEURL + 'variavel/novo')
    });
  });

  $(document).on('click', '#cadastrar-variavel', function(e){
    e.preventDefault();
    cadastrarVariavel();
  });

  $(document).on('click', '#cancelar-variavel', function(e){
    e.preventDefault();
    $('.bootbox').modal('hide');
  });

  function cadastrarVariavel() {
    if (validarCamposVariavel()) {
      var dados = {
        'id_projeto' : $('#id_projeto').val(),
        'nome' : $('#nome').val(),
        'inicio_universo' : $('#inicio_universo').val(),
        'fim_universo' : $('#fim_universo').val(),
        'unidade_medida' : $('#unidade_medida').val(),
        'objetiva' : $('#objetiva').is(':checked') ? 1 : 0
      };
      $.ajax({
        dataType: "json",
        type: "POST",
        url: BASEURL + "variavel/salvar",
        data: dados,
        async: false,
        beforeSend: function(){
          $('#cadastrar-variavel').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
          $('#cadastrar-variavel').addClass('disabled');
          $('#cancelar-variavel').addClass('disabled');
        },
        success: function(response) {
          carregarVariaveis();
          mostraMensagem(response.title, response.text);
          $('.bootbox').modal('hide');
        },
        error: function(response) {
          modalAviso(response.responseText);
        }
      });
    }
  };

  function validarCamposVariavel() {
    var mensagem = '<strong>Preencha os campos obrigatórios: </strong><br/>';
    var erro = 0;
    if ($('#variavel-form #nome').val() === "") {
      mensagem += '- Nome <br/>';
      erro = 1;
    }
    if ($('#variavel-form #inicio_universo').val() === "") {
      mensagem += '- Início Universo <br/>';
      erro = 1;
    }
    if ($('#variavel-form #fim_universo').val() === "") {
      mensagem += '- Fim Universo <br/>';
      erro = 1;
    }
    if ($('#variavel-form #inicio_universo').val() !== "" && $('#variavel-form #fim_universo').val() !== "" && parseFloat($('#variavel-form #inicio_universo').val()) >= parseFloat($('#variavel-form #fim_universo').val())) {
      mensagem += '- Valor de Início deve ser menor do que o Fim <br/>';
      erro = 1;
    }
    if ($('#variavel-form #unidade_medida').val() === "") {
      mensagem += '- Unidade Medida <br/>';
      erro = 1;
    }
    if (erro > 0) {
      modalAviso(mensagem);
      return false;
    } else{
      return true;
    }
  }
  // Fim Nova Variável /////////////////////////////////////////////////////////

  // Remover Variável //////////////////////////////////////////////////////////
  $(document).on('click', '.remove-variavel', function(e){
    e.preventDefault();
    var id = parseInt($(this).find('input[type="hidden"]').val());
    bootbox.dialog({
      title: "<h3 class='smaller lighter red no-margin'>Atenção!</h3>",
      message: "Você tem certeza que deseja <strong>remover</strong> a variável?",
      buttons: {
        "success" : {
          "label" : "<i class='ace-icon fa fa-check'></i> Sim",
          "className" : "btn-sm btn-success margin-3",
          callback: function() {
            removerVariavel(id);
          }
        },
        danger: {
          label: "<i class='ace-icon fa fa-times'></i> Não",
          className: "btn-sm btn-danger margin-3"
        }
      }
    });
  });

  function removerVariavel(id) {
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "variavel/remover",
      data: {id: id},
      async: false,
      beforeSend: function(){
        $('.modal-footer .btn-success').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
        $('.modal-footer .btn-sm').prop('disabled', true);
      },
      success: function(response) {
        carregarVariaveis();
        mostraMensagem(response.title, response.text);
      },
      error: function(response){
        modalAviso(response.responseText);
      }
    });
  };
  // Fim Remover Variável //////////////////////////////////////////////////////

  // Validar Variável //////////////////////////////////////////////////////////
  function validarVariaveis() {
    var retorno = false;
    $.ajax({
      type: "POST",
      url: BASEURL + "variavel/validar",
      data: {id_projeto: $('#id_projeto').val()},
      async: false,
      beforeSend: function(){
        $(".carregando").removeClass('hide');
      },
      success: function(response) {
        if (response === 'sucesso') {
            retorno = true;
        } else {
            modalAviso(response);
        }
      }
    });
    return retorno;
  }
  // Fim Validar Variável //////////////////////////////////////////////////////

  // Novo Termo ////////////////////////////////////////////////////////////////
  $(document).on('click', '.novo-termo', function(e){
    e.preventDefault();
    $('#id_variavel').val(parseInt($(this).find('input[type="hidden"]').val()));
    bootbox.dialog({
      title: "<h4 class='blue bigger'>Novo Termo</h4>",
      message: $('<div>Carregando...</div>').load(BASEURL + 'termo/novo')
    });
  });

  $(document).on('click', '#cadastrar-termo', function(e){
    e.preventDefault();
    cadastrarTermo();
  });

  $(document).on('click', '#cancelar-termo', function(e){
    e.preventDefault();
    $('.bootbox').modal('hide');
  });

  function cadastrarTermo() {
    if (validarCamposTermo()) {
      var dados = {
        'id_variavel' : $('#id_variavel').val(),
        'nome' : $('#nome').val(),
        'inicio_suporte' : $('#inicio_suporte').val(),
        'fim_suporte' : $('#fim_suporte').val(),
        'inicio_nucleo' : $('#inicio_nucleo').val(),
        'fim_nucleo' : $('#fim_nucleo').val(),
      };
      $.ajax({
        dataType: "json",
        type: "POST",
        url: BASEURL + "termo/salvar",
        data: dados,
        async: false,
        beforeSend: function(){
          $('#cadastrar-termo').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
          $('#cadastrar-termo').addClass('disabled');
          $('#cancelar-termo').addClass('disabled');
        },
        success: function(response) {
          carregarTermos();
          mostraMensagem(response.title, response.text);
          $('.bootbox').modal('hide');
        },
        error: function(response) {
          modalAviso(response.responseText);
        }
      });
    }
  };

  function validarCamposTermo() {
    var mensagem = '<strong>Preencha os campos obrigatórios: </strong><br/>';
    var erro = 0;
    if ($('#termo-form #nome').val() === "") {
      mensagem += '- Nome <br/>';
      erro = 1;
    }
    if ($('#termo-form #inicio_suporte').val() === "") {
      mensagem += '- Início Suporte <br/>';
      erro = 1;
    }
    if ($('#termo-form #fim_suporte').val() === "") {
      mensagem += '- Fim Suporte <br/>';
      erro = 1;
    }
    if ($('#termo-form #inicio_suporte').val() != "" && $('#termo-form #fim_suporte').val() != "" && parseFloat($('#termo-form #inicio_suporte').val()) >= parseFloat($('#termo-form #fim_suporte').val())) {
      mensagem += '- Valor de Início Suporte deve ser menor do que o Fim Suporte <br/>';
      erro = 1;
    }
    if ($('#termo-form #inicio_nucleo').val() === "") {
      mensagem += '- Início Núcleo <br/>';
      erro = 1;
    }
    if ($('#termo-form #fim_nucleo').val() === "") {
      mensagem += '- Fim Núcleo <br/>';
      erro = 1;
    }
    if ($('#termo-form #inicio_nucleo').val() != "" && $('#termo-form #fim_nucleo').val() != "" && parseFloat($('#termo-form #inicio_nucleo').val()) > parseFloat($('#termo-form #fim_nucleo').val())) {
      mensagem += '- Valor de Início Núcleo deve ser menor ou igual do que o Fim Núcleo <br/>';
      erro = 1;
    }
    if (erro > 0) {
      modalAviso(mensagem);
      return false;
    } else{
      return true;
    }
  }
  // Fim Novo Termo ////////////////////////////////////////////////////////////

  // Remover Termo /////////////////////////////////////////////////////////////
  $(document).on('click', '.remove-termo', function(e){
    e.preventDefault();
    var id = parseInt($(this).find('input[type="hidden"]').val());
    bootbox.dialog({
      title: "<h3 class='smaller lighter red no-margin'>Atenção!</h3>",
      message: "Você tem certeza que deseja <strong>remover</strong> o termo?",
      buttons: {
        "success" : {
          "label" : "<i class='ace-icon fa fa-check'></i> Sim",
          "className" : "btn-sm btn-success margin-3",
          callback: function() {
            removerTermo(id);
          }
        },
        danger: {
          label: "<i class='ace-icon fa fa-times'></i> Não",
          className: "btn-sm btn-danger margin-3"
        }
      }
    });
  });

  function removerTermo(id) {
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "termo/remover",
      data: {id: id},
      async: false,
      beforeSend: function(){
        $('.modal-footer .btn-success').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
        $('.modal-footer .btn-sm').prop('disabled', true);
      },
      success: function(response) {
        carregarTermos();
        mostraMensagem(response.title, response.text);
      },
      error: function(response){
        modalAviso(response.responseText);
      }
    });
  };
  // Fim Remover Termo /////////////////////////////////////////////////////////

  // Validar Termo /////////////////////////////////////////////////////////////
  function validarTermos() {
    var retorno = false;
    $.ajax({
      type: "POST",
      url: BASEURL + "termo/validar",
      data: {id_projeto: $('#id_projeto').val()},
      async: false,
      beforeSend: function(){
        $(".carregando").removeClass('hide');
      },
      success: function(response) {
        if (response === 'sucesso') {
            retorno = true;
        } else {
            modalAviso(response);
        }
      }
    });
    return retorno;
  }
  // Fim Validar Termo /////////////////////////////////////////////////////////

  // Nova Regra ////////////////////////////////////////////////////////////////
  $(document).on('click', '.nova-regra', function(e){
    e.preventDefault();
    if ($('#regra-form').valid()) {
      cadastrarRegra();
    }
  });

  function cadastrarRegra() {
    var dados = {
      'id_projeto' : $('#id_projeto').val(),
      'id_termos_antecedentes' : $('#id_termos_antecedentes').val(),
      'operador' : $('#operador').val(),
      'id_termo_consequente' : $('#id_termo_consequente').val(),
    };
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "regra/salvar",
      data: dados,
      async: false,
      beforeSend: function(){
        $(".carregando").removeClass('hide');
      },
      success: function(response) {
        carregarRegras();
        mostraMensagem(response.title, response.text);
      },
      error: function(response) {
        modalAviso(response.responseText);
      }
    });
  };
  // Fim Nova Regra ////////////////////////////////////////////////////////////

  // Remover Regra /////////////////////////////////////////////////////////////
  $(document).on('click', '.remove-regra', function(e){
    e.preventDefault();
    var id = parseInt($(this).find('input[type="hidden"]').val());
    bootbox.dialog({
      title: "<h3 class='smaller lighter red no-margin'>Atenção!</h3>",
      message: "Você tem certeza que deseja <strong>remover</strong> a regra?",
      buttons: {
        "success" : {
          "label" : "<i class='ace-icon fa fa-check'></i> Sim",
          "className" : "btn-sm btn-success margin-3",
          callback: function() {
            removerRegra(id);
          }
        },
        danger: {
          label: "<i class='ace-icon fa fa-times'></i> Não",
          className: "btn-sm btn-danger margin-3"
        }
      }
    });
  });

  function removerRegra(id) {
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "regra/remover",
      data: {id: id},
      async: false,
      beforeSend: function(){
        $('.modal-footer .btn-success').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
        $('.modal-footer .btn-sm').prop('disabled', true);
      },
      success: function(response) {
        carregarRegras();
        mostraMensagem(response.title, response.text);
      },
      error: function(response){
        modalAviso(response.responseText);
      }
    });
  };
  // Fim Remover Regra /////////////////////////////////////////////////////////

  // Validar Regra /////////////////////////////////////////////////////////////
  function validarRegra() {
    var retorno = false;
    $.ajax({
      type: "POST",
      url: BASEURL + "regra/validar",
      data: {id_projeto: $('#id_projeto').val()},
      async: false,
      beforeSend: function(){
        $(".carregando").removeClass('hide');
      },
      success: function(response) {
        if (response === 'sucesso') {
            retorno = true;
        } else {
            modalAviso(response);
        }
      }
    });
    return retorno;
  }
  // Fim Validar Regra /////////////////////////////////////////////////////////

  // Inferir ///////////////////////////////////////////////////////////////////
  function inferir() {
    var variaveis = [];
    $(".valor-variaveis").each( function(index, value) {
      var id = parseInt($(this).attr("id").replace('valor-', ''));
      variaveis[id] = $(this).val() + "";
    });
    var dados = {
      'id_projeto' : $('#id_projeto').val(),
      'variaveis' : variaveis
    };
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "projeto/inferir",
      data: dados,
      async: false,
      beforeSend: function(){
        $(".carregando").removeClass('hide');
        $("#regras-pertinencia").addClass('hide');
        $("#table-regras-pertinencia").html("");
        $("#table-termos-consequentes").html("");
        $("#centroide").html("?");
      },
      success: function(response) {
        var htmlRegras = "<tbody>";
        $.each(response.regras, function (index, value) {
            htmlRegras += '<tr><td>' + value + '</td></tr>';
        });
        htmlRegras += "</tbody>";
        $("#table-regras-pertinencia").html(htmlRegras);

        var htmlTermos = "<tbody>";
        $.each(response.pertinencias, function (index, value) {
            htmlTermos += '<tr><td>' + value + '</td></tr>';
        });
        htmlTermos += "</tbody>";
        $("#table-termos-consequentes").html(htmlTermos);

        $("#centroide").html(response.centroide + " %");
        $("#regras-pertinencia").removeClass('hide');
        montarGrafico(response.grafico);
        mostraMensagem("Sucesso", "Inferência realizada.");
      },
      error: function(response){
        modalAviso("Ocorreu algum erro no processo de inferência.");
      },
      complete: function(){
        $(".carregando").addClass('hide');
      }
    });
  }
  // Fim Inferir ///////////////////////////////////////////////////////////////

  // Montar Gráfico ////////////////////////////////////////////////////////////
  function montarGrafico(response) {
    $(".grafico-variavel-objetiva").each( function(index, value) {
        var id = $(this).attr("id");
        geraGraficoLineBasic("grafico-variavel-objetiva-"+id, response.variavel, response.min, response.max, response.series);
    });
  };
  // Fim Montar Gráfico ////////////////////////////////////////////////////////

  function mostraMensagem(title, text) {
    $.gritter.add({
      fade_in_speed: 'medium',
      fade_out_speed: 'medium',
      time: 3000,
      title: '<i class="ace-icon fa fa-check"></i> ' + title,
      text: text,
      class_name: 'gritter-success'
    });
  };

  function modalAviso(mensagem) {
    bootbox.dialog({
      title: "<h3 class='smaller lighter red no-margin'>Atenção!</h3>",
      message: mensagem,
      buttons: {
        danger: {
          label: "<i class='ace-icon fa fa-times'></i> Fechar",
          className: "btn btn-sm btn-danger pull-right"
        }
      }
    });
  }
});