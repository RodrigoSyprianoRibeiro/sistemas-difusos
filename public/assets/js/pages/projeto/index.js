jQuery(function($) {

  carregarProjetos();

  function carregarProjetos() {
    $('#projetos').load(BASEURL + 'projeto/carregar');
  }

  // Novo Projeto //////////////////////////////////////////////////////////////
  $(document).on('click', '.novo-projeto', function(e){
    e.preventDefault();
    bootbox.dialog({
      title: "<h4 class='blue bigger'>Novo Projeto</h4>",
      message: $('<div>Carregando...</div>').load(BASEURL + 'projeto/novo')
    });
  });

  $(document).on('click', '#cadastrar-projeto', function(e){
    e.preventDefault();
    cadastrarProjeto();
  });

  $(document).on('click', '#cancelar-projeto', function(e){
    e.preventDefault();
    $('.bootbox').modal('hide');
  });

  function cadastrarProjeto() {
    if (validarCamposProjeto()) {
      var dados = {
        'nome' : $('#nome').val(),
        'descricao' : $('#descricao').val()
      };
      $.ajax({
        dataType: "json",
        type: "POST",
        url: BASEURL + "projeto/salvar",
        data: dados,
        async: false,
        beforeSend: function(){
          $('#cadastrar-projeto').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
          $('#cadastrar-projeto').addClass('disabled');
          $('#cancelar-projeto').addClass('disabled');
        },
        success: function(response) {
          carregarProjetos();
          mostraMensagem(response.title, response.text);
          $('.bootbox').modal('hide');
        },
        error: function(response) {
          modalAviso(response.responseText);
        }
      });
    }
  };

  function validarCamposProjeto() {
    var mensagem = '<strong>Preencha os campos obrigatórios: </strong><br/>';
    var erro = 0;
    if ($('#projeto-form #nome').val() === "") {
      mensagem += '- Nome <br/>';
      erro = 1;
    }
    if ($('#projeto-form #descricao').val() === "") {
      mensagem += '- Descrição <br/>';
      erro = 1;
    }
    if (erro > 0) {
      modalAviso(mensagem);
      return false;
    } else{
      return true;
    }
  }
  // Fim Novo Projeto //////////////////////////////////////////////////////////

  // Remover Projeto ///////////////////////////////////////////////////////////
  $(document).on('click', '.remover-projeto', function(e){
    e.preventDefault();
    var id = parseInt($(this).find('input[type="hidden"]').val());
    bootbox.dialog({
      title: "<h3 class='smaller lighter red no-margin'>Atenção!</h3>",
      message: "Você tem certeza que deseja <strong>remover</strong> o projeto?",
      buttons: {
        "success" : {
          "label" : "<i class='ace-icon fa fa-check'></i> Sim",
          "className" : "btn-sm btn-success margin-3",
          callback: function() {
            removerProjeto(id);
          }
        },
        danger: {
          label: "<i class='ace-icon fa fa-times'></i> Não",
          className: "btn-sm btn-danger margin-3"
        }
      }
    });
  });

  function removerProjeto(id) {
    $.ajax({
      dataType: "json",
      type: "POST",
      url: BASEURL + "projeto/remover",
      data: {id: id},
      async: false,
      beforeSend: function(){
        $('.modal-footer .btn-success').html('<i class=" ace-icon loading-icon fa fa-spinner fa-spin white"></i> Carregando...');
        $('.modal-footer .btn-sm').prop('disabled', true);
      },
      success: function(response) {
        carregarProjetos();
        mostraMensagem(response.title, response.text);
      },
      error: function(response){
        modalAviso(response.responseText);
      }
    });
  };
  // Fim Remover Projeto ///////////////////////////////////////////////////////

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