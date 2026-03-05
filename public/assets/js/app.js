$(document).ready(function () {
    const apiURL = "http://localhost/desafio-manageasy/public/api.php";
    let paginaAtual = 1;
    const limite = 10;
    
    const modalBootstrap = new bootstrap.Modal(document.getElementById('modalContato'));
    const toastBootstrap = new bootstrap.Toast(document.getElementById('toastNotificacao'));

    // --- LOCAL STORAGE: Recuperar Filtro ao Iniciar ---
    const filtroSalvo = localStorage.getItem('filtro-gerenciador');
    if (filtroSalvo) {
        $("#filtro-busca").val(filtroSalvo);
    }

    function exibirToast(mensagem, tipo = "success") {
        const toastDiv = $("#toastNotificacao");
        toastDiv.removeClass("bg-success bg-danger bg-warning");
        toastDiv.addClass(`bg-${tipo}`);
        $("#toastMensagem").text(mensagem);
        toastBootstrap.show();
    }

    function formatarDataBR(dataString) {
        if (!dataString) return "-";
        const dataApenas = dataString.split(" ")[0];
        const partes = dataApenas.split("-");
        return partes.length !== 3 ? dataString : `${partes[2]}/${partes[1]}/${partes[0]}`;
    }

    function buscarContatos(pagina = 1) {
        const termoBusca = $("#filtro-busca").val();
        paginaAtual = pagina;

        // --- LOCAL STORAGE: Salvar Filtro ao Buscar ---
        localStorage.setItem('filtro-gerenciador', termoBusca);

        $.ajax({
            url: apiURL,
            type: "GET",
            data: { search: termoBusca, page: paginaAtual, limit: limite },
            dataType: "json",
            success: function (response) {
                renderizarTabela(response.data);
                renderizarPaginacao(response.totalPages, response.page);
            },
            error: function () {
                $("#tabela-contatos").html('<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados.</td></tr>');
            }
        });
    }

    function renderizarTabela(contatos) {
        let html = "";
        if (contatos && contatos.length > 0) {
            contatos.forEach((c) => {
                html += `
                    <tr>
                        <td>${c.id}</td>
                        <td>${c.nome}</td>
                        <td>${c.email}</td>
                        <td>${c.telefone || '-'}</td>
                        <td>${formatarDataBR(c.data_nascimento)}</td>
                        <td>${formatarDataBR(c.created_at)}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-warning btn-editar" data-id="${c.id}">
                                    <i class="bi bi-pencil-square"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-danger btn-excluir" data-id="${c.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
        } else {
            html = '<tr><td colspan="7" class="text-center">Nenhum registro encontrado.</td></tr>';
        }
        $("#tabela-contatos").html(html);
    }

    function renderizarPaginacao(totalPaginas, paginaAtiva) {
        let htmlPaginacao = "";
        for (let i = 1; i <= totalPaginas; i++) {
            const classeAtiva = i === parseInt(paginaAtiva) ? "active" : "";
            htmlPaginacao += `<li class="page-item ${classeAtiva}"><a class="page-link btn-ir-pagina" href="#" data-pagina="${i}">${i}</a></li>`;
        }
        $("#paginacao").html(htmlPaginacao);
    }

    // --- LOGICA DO MODAL ---

    $("#btn-novo-contato").on("click", function() {
        $("#form-contato")[0].reset();
        $("#form-contato").removeClass("was-validated");
        $("#contato-id").val(""); 
        $("#modalTitulo").text("Novo Contato");
        modalBootstrap.show();
    });

    // EDITAR: Com correção de cache e limpeza total
    // Abrir para EDITAR
$(document).on("click", ".btn-editar", function () {
    const id = $(this).data("id");
    const $btn = $(this);
    
    // 1. Bloqueia o botão para evitar múltiplos cliques
    $btn.prop('disabled', true); 

    // 2. LIMPEZA MANUAL FORÇADA (Zera tudo antes da requisição)
    $("#contato-id").val("");
    $("#nome").val("");
    $("#email").val("");
    $("#telefone").val("");
    $("#data_nascimento").val("");
    $("#form-contato").removeClass("was-validated");
    $("#modalTitulo").text("Carregando dados...");

    // 3. Busca os dados com prevenção de cache
    $.ajax({
        url: apiURL,
        type: "GET",
        data: { id: id, _t: new Date().getTime() }, 
        dataType: "json",
        success: function(response) {
            // Tenta extrair o contato de diferentes formatos de retorno
            let c = null;
            if (response.data) {
                c = Array.isArray(response.data) ? response.data[0] : response.data;
            } else {
                c = Array.isArray(response) ? response[0] : response;
            }

            if (c) {
                // 4. Preenche os campos UM POR UM garantindo os novos valores
                $("#contato-id").val(c.id);
                $("#nome").val(c.nome);
                $("#email").val(c.email);
                $("#telefone").val(c.telefone);
                $("#data_nascimento").val(c.data_nascimento);
                
                $("#modalTitulo").text("Editar Contato #" + c.id);
                
                // 5. SÓ ABRE O MODAL AGORA, com os dados já injetados
                modalBootstrap.show();
            } else {
                exibirToast("Erro: Contato não encontrado no banco.", "danger");
            }
        },
        error: function() {
            exibirToast("Erro de comunicação com o servidor.", "danger");
        },
        complete: function() {
            // 6. Libera o botão da tabela
            $btn.prop('disabled', false);
        }
    });
});

    $("#form-contato").on("submit", function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            $(this).addClass("was-validated");
            return;
        }

        const id = $("#contato-id").val();
        const dados = {
            nome: $("#nome").val().trim(),
            email: $("#email").val().trim(),
            telefone: $("#telefone").val().trim(),
            data_nascimento: $("#data_nascimento").val()
        };

        const metodo = id ? "PUT" : "POST"; 
        const urlFinal = id ? `${apiURL}?id=${id}` : apiURL;

        $("#btn-salvar").prop("disabled", true).text("Gravando...");

        $.ajax({
            url: urlFinal,
            type: metodo,
            contentType: "application/json",
            data: JSON.stringify(dados),
            success: function() {
                modalBootstrap.hide();
                buscarContatos(paginaAtual);
                exibirToast(id ? "Atualizado com sucesso!" : "Criado com sucesso!");
            },
            error: function(xhr) {
                exibirToast("Erro: " + xhr.responseText, "danger");
            },
            complete: function() {
                $("#btn-salvar").prop("disabled", false).text("Salvar");
            }
        });
    });

    $(document).on("click", ".btn-excluir", function () {
        const id = $(this).data("id");
        if (confirm(`Deseja realmente excluir o contato #${id}?`)) {
            $.ajax({
                url: `${apiURL}?id=${id}`,
                type: "DELETE",
                success: function () { 
                    exibirToast("Removido com sucesso.", "warning");
                    buscarContatos(paginaAtual); 
                }
            });
        }
    });

    // Botão Limpar: Além de limpar o campo, limpa o LocalStorage
    $("#btn-limpar").on("click", function() {
        $("#filtro-busca").val("");
        localStorage.removeItem('filtro-gerenciador');
        buscarContatos(1);
    });

    $("#btn-buscar").on("click", () => buscarContatos(1));
    
    $(document).on("click", ".btn-ir-pagina", function (e) {
        e.preventDefault();
        buscarContatos($(this).data("pagina"));
    });

    // Inicia a busca
    buscarContatos(paginaAtual);
});