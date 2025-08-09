# Gerenciador de Eventos (eventos-manager)

Plugin WordPress para cadastro, gerenciamento e exibição de eventos com calendário interativo, lista de próximos eventos e integração via shortcodes e widget.

## Funcionalidades

- Cadastro de eventos personalizados (CPT: `evento`)
- Metabox para data, hora e local do evento
- Taxonomia personalizada para tipos de evento
- Calendário interativo (FullCalendar.js) com AJAX
- Lista de próximos eventos filtrável por tipo e limite
- Shortcodes para exibição do calendário, lista e widget
- Página única customizada para eventos (`single-evento.php`)
- Widget para exibir próximos eventos na sidebar
- Página de ajuda no admin

## Instalação

1. Faça upload da pasta `eventos-manager` para o diretório `wp-content/plugins/`.
2. Ative o plugin no painel do WordPress.
3. O tipo de post "Evento" estará disponível no menu lateral.

## Shortcodes Disponíveis

- `[mostra-calendario]` — Exibe o calendário de eventos.
- `[mostra-prox-eventos limit="5" tipo=""]` — Lista os próximos eventos. Parâmetros:
  - `limit`: número de eventos (padrão: 5)
  - `tipo`: slug do tipo de evento (opcional)
- `[eventos-completo]` — Exibe calendário e lista de próximos eventos juntos.
- `[mostra-calendario-widget]` — Exibe um calendário compacto para sidebar ou widgets.

## Widget

- Vá em **Aparência > Widgets** e adicione o widget "Próximos Eventos" à sua sidebar.
- Configure o título e o limite de eventos a exibir.

## Custom Post Type e Taxonomia

- **Post Type:** `evento`
- **Taxonomia:** `tipo_evento` (hierárquica)

## Templates

- O plugin inclui o template `single-evento.php` para exibição individual dos eventos.

## Scripts e Estilos

- FullCalendar.js e Moment.js são carregados automaticamente.
- Estilos customizados para admin e frontend em `/assets/css/`.

## AJAX

- Os eventos do calendário são carregados via AJAX para melhor performance.

## Página de Ajuda

- Acesse em **Eventos > Ajuda** para instruções e exemplos de uso dos shortcodes.

## Autor

Marco Antonio Vivas

---

**Observação:**
- Para personalizações avançadas, edite os arquivos em `includes/` e os templates conforme necessário.
- Compatível com WordPress 5.0+.