@extends('layouts.app')

@section('title', 'IMG a PDF')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
<style>
/* ── Variables modo claro (default del sistema) ── */
.img2pdf-app {
    --t-bg0:#E8ECF5;--t-bg1:#F4F6FC;--t-bg2:#FFFFFF;--t-bg3:#EAEFFE;
    --t-accent:#4F8EF7;--t-accent2:#7C5BF5;--t-accent3:#38D9A9;
    --t-text1:#0D1126;--t-text2:#4A5278;--t-text3:#9BA3C4;
    --t-border:#D0D6EE;--t-danger:#F85A5A;--t-warn:#F7A74F;--t-success:#38D9A9;
    --t-radius:12px;--t-radius-sm:8px;--t-radius-lg:18px;
    --t-shadow:0 4px 24px rgba(80,100,180,0.1);
}
/* ── Variables modo oscuro ── */
[data-theme="dark"] .img2pdf-app {
    --t-bg0:#0D0F12;--t-bg1:#141720;--t-bg2:#1C2030;--t-bg3:#242840;
    --t-accent:#4F8EF7;--t-accent2:#7C5BF5;--t-accent3:#38D9A9;
    --t-text1:#F0F2FF;--t-text2:#9BA3C4;--t-text3:#5C647A;
    --t-border:#2A2F45;--t-danger:#F85A5A;--t-warn:#F7A74F;--t-success:#38D9A9;
    --t-shadow:0 4px 24px rgba(0,0,0,0.4);
}

/* ── Layout principal ── */
.img2pdf-app {
    display:grid;
    grid-template-columns:260px 1fr 300px;
    height:600px;
    background:var(--t-bg0);
    border-radius:14px;
    overflow:hidden;
    box-shadow:var(--t-shadow);
    border:1px solid var(--t-border);
    font-family:'DM Sans','Segoe UI',sans-serif;
    font-size:14px;
}

/* ── Sidebar ── */
.t-sidebar{background:var(--t-bg1);border-right:1px solid var(--t-border);display:flex;flex-direction:column;overflow:hidden}
.t-sidebar-header{padding:14px 16px;border-bottom:1px solid var(--t-border);display:flex;align-items:center;justify-content:space-between}
.t-sidebar-title{font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--t-text3)}
.t-count-badge{background:var(--t-accent);color:#fff;border-radius:99px;padding:1px 8px;font-size:11px;font-weight:600}
.t-drop-zone{margin:12px;border:1.5px dashed var(--t-border);border-radius:var(--t-radius);padding:24px 12px;text-align:center;cursor:pointer;transition:all .25s}
.t-drop-zone:hover,.t-drop-zone.drag-over{border-color:var(--t-accent);background:rgba(79,142,247,.06)}
.t-drop-icon{font-size:28px;color:var(--t-text3);margin-bottom:8px;transition:color .2s}
.t-drop-zone:hover .t-drop-icon,.t-drop-zone.drag-over .t-drop-icon{color:var(--t-accent)}
.t-drop-text{font-size:12px;color:var(--t-text2);line-height:1.5}
.t-drop-text strong{color:var(--t-text1)}
.t-drop-ext{display:flex;flex-wrap:wrap;gap:3px;justify-content:center;margin-top:8px}
.t-ext-pill{background:var(--t-bg3);color:var(--t-text3);font-size:9px;font-weight:600;padding:2px 6px;border-radius:4px;letter-spacing:.05em}
.t-thumb-list{flex:1;overflow-y:auto;padding:4px 8px 8px}
.t-thumb-list::-webkit-scrollbar{width:4px}
.t-thumb-list::-webkit-scrollbar-track{background:transparent}
.t-thumb-list::-webkit-scrollbar-thumb{background:var(--t-border);border-radius:2px}
.t-thumb-item{display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:var(--t-radius-sm);cursor:pointer;transition:all .2s;border:1.5px solid transparent;margin-bottom:3px;position:relative}
.t-thumb-item:hover{background:var(--t-bg2)}
.t-thumb-item.selected{background:var(--t-bg3);border-color:var(--t-accent)}
.t-thumb-item.dragging{opacity:.4}
.t-thumb-preview{width:36px;height:36px;border-radius:6px;object-fit:cover;background:var(--t-bg3);flex-shrink:0}
.t-thumb-info{flex:1;min-width:0}
.t-thumb-name{font-size:12px;font-weight:500;color:var(--t-text1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.t-thumb-size{font-size:10px;color:var(--t-text3)}
.t-thumb-idx{width:18px;height:18px;background:var(--t-bg3);border-radius:4px;font-size:10px;font-weight:700;color:var(--t-text3);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-family:'Space Mono',monospace}
.t-thumb-actions{display:none;gap:2px;position:absolute;right:6px;top:50%;transform:translateY(-50%)}
.t-thumb-item:hover .t-thumb-actions{display:flex}
.t-thumb-act-btn{width:22px;height:22px;border-radius:4px;border:none;background:var(--t-bg2);color:var(--t-text2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;transition:all .15s}
.t-thumb-act-btn.del:hover{background:rgba(248,90,90,.15);color:var(--t-danger)}
.t-thumb-act-btn:hover{background:var(--t-bg3);color:var(--t-text1)}
.t-sidebar-footer{padding:10px 12px;border-top:1px solid var(--t-border)}
.t-clear-btn{width:100%;padding:7px;border-radius:var(--t-radius-sm);border:1px solid var(--t-border);background:transparent;color:var(--t-text3);font-family:'DM Sans',sans-serif;font-size:12px;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px}
.t-clear-btn:hover{border-color:var(--t-danger);color:var(--t-danger)}

/* ── Main preview ── */
.t-main{background:var(--t-bg0);display:flex;flex-direction:column;overflow:hidden}
.t-preview-wrap{flex:1;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;padding:20px}
.t-empty-state{text-align:center;color:var(--t-text3)}
.t-empty-icon{font-size:52px;margin-bottom:16px;opacity:.4}
.t-empty-title{font-size:16px;font-weight:500;color:var(--t-text2);margin-bottom:6px}
.t-empty-sub{font-size:13px;line-height:1.6}
.t-preview-img-wrap{position:relative;max-width:100%;max-height:100%}
.t-preview-img{max-width:100%;max-height:100%;border-radius:var(--t-radius);box-shadow:var(--t-shadow);display:block;transition:transform .3s}
.t-preview-toolbar{position:absolute;bottom:-48px;left:50%;transform:translateX(-50%);display:flex;gap:6px;background:var(--t-bg2);border:1px solid var(--t-border);border-radius:99px;padding:6px 10px;transition:bottom .25s}
.t-preview-img-wrap:hover .t-preview-toolbar{bottom:12px}
.t-pt-btn{padding:5px 10px;border-radius:99px;border:none;background:transparent;color:var(--t-text2);font-family:'DM Sans',sans-serif;font-size:12px;cursor:pointer;display:flex;align-items:center;gap:4px;transition:all .2s}
.t-pt-btn:hover{background:var(--t-bg3);color:var(--t-text1)}
.t-pt-btn.danger:hover{color:var(--t-danger)}
.t-progress-section{padding:0 20px 14px}
.t-progress-bar-wrap{height:4px;background:var(--t-bg2);border-radius:2px;overflow:hidden;margin-bottom:6px}
.t-progress-bar{height:100%;background:linear-gradient(90deg,var(--t-accent),var(--t-accent2));border-radius:2px;transition:width .4s ease;width:0%}
.t-progress-text{font-size:11px;color:var(--t-text3);text-align:center}

/* ── Right panel ── */
.t-panel{background:var(--t-bg1);border-left:1px solid var(--t-border);display:flex;flex-direction:column;overflow-y:auto}
.t-panel::-webkit-scrollbar{width:4px}
.t-panel::-webkit-scrollbar-thumb{background:var(--t-border);border-radius:2px}
.t-panel-section{padding:14px 16px;border-bottom:1px solid var(--t-border)}
.t-panel-label{font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:var(--t-text3);margin-bottom:10px}
.t-row2{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.t-field-group{display:flex;flex-direction:column;gap:5px}
.t-field-label{font-size:11px;color:var(--t-text2)}
.t-field-select,.t-field-input{background:var(--t-bg2);border:1px solid var(--t-border);border-radius:var(--t-radius-sm);color:var(--t-text1);font-family:'DM Sans',sans-serif;font-size:12px;padding:6px 10px;width:100%;cursor:pointer;transition:border .2s;outline:none;-webkit-appearance:none}
.t-field-select:focus,.t-field-input:focus{border-color:var(--t-accent)}
.t-field-input[type="number"]{-moz-appearance:textfield}
.t-toggle-row{display:flex;align-items:center;justify-content:space-between;padding:3px 0}
.t-toggle-label{font-size:12px;color:var(--t-text2)}
.t-toggle{position:relative;width:36px;height:20px;cursor:pointer}
.t-toggle input{opacity:0;width:0;height:0;position:absolute}
.t-toggle-track{position:absolute;inset:0;background:var(--t-border);border-radius:10px;transition:background .2s}
.t-toggle input:checked~.t-toggle-track{background:var(--t-accent)}
.t-toggle-thumb{position:absolute;top:3px;left:3px;width:14px;height:14px;background:#fff;border-radius:50%;transition:transform .2s}
.t-toggle input:checked~.t-toggle-track~.t-toggle-thumb{transform:translateX(16px)}
.t-slider-wrap{display:flex;align-items:center;gap:8px}
.t-slider{flex:1;-webkit-appearance:none;height:4px;background:var(--t-border);border-radius:2px;outline:none;cursor:pointer}
.t-slider::-webkit-slider-thumb{-webkit-appearance:none;width:14px;height:14px;border-radius:50%;background:var(--t-accent);cursor:pointer;box-shadow:0 0 0 3px rgba(79,142,247,.2)}
.t-slider-val{font-size:12px;font-weight:600;color:var(--t-text1);min-width:32px;text-align:right;font-family:'Space Mono',monospace}
.t-stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.t-stat-card{background:var(--t-bg2);border-radius:var(--t-radius-sm);padding:10px;text-align:center}
.t-stat-val{font-size:18px;font-weight:600;color:var(--t-text1);font-family:'Space Mono',monospace}
.t-stat-lbl{font-size:10px;color:var(--t-text3);margin-top:2px}
.t-convert-btn{margin:14px 16px;padding:12px;border-radius:var(--t-radius);border:none;background:linear-gradient(135deg,var(--t-accent),var(--t-accent2));color:#fff;font-family:'DM Sans',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:all .25s;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:.02em}
.t-convert-btn:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(79,142,247,.35)}
.t-convert-btn:active{transform:translateY(0)}
.t-convert-btn:disabled{opacity:.4;cursor:not-allowed;transform:none}
#tPreviewBtn:not(:disabled):hover{background:var(--t-bg3)!important;border-color:var(--t-accent)!important;color:var(--t-accent)!important;opacity:1}

/* ── History ── */
.t-history-item{display:flex;align-items:center;gap:10px;padding:8px;border-radius:var(--t-radius-sm);border:1px solid var(--t-border);margin-bottom:6px;background:var(--t-bg2)}
.t-history-icon{width:32px;height:32px;background:rgba(248,90,90,.12);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--t-danger);font-size:16px;flex-shrink:0}
.t-history-info{flex:1;min-width:0}
.t-history-name{font-size:12px;font-weight:500;color:var(--t-text1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.t-history-meta{font-size:10px;color:var(--t-text3)}
.t-history-dl{width:26px;height:26px;border-radius:6px;border:1px solid var(--t-border);background:transparent;color:var(--t-text2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:all .2s}
.t-history-dl:hover{border-color:var(--t-accent);color:var(--t-accent)}

/* ── Toast ── */
.t-toast-wrap{position:fixed;bottom:80px;right:20px;display:flex;flex-direction:column;gap:8px;z-index:1055;pointer-events:none}
.t-toast{background:var(--t-bg2);border:1px solid var(--t-border);border-radius:var(--t-radius);padding:10px 14px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--t-text1);min-width:220px;transform:translateX(120%);transition:transform .35s cubic-bezier(.34,1.56,.64,1);pointer-events:auto}
.t-toast.show{transform:translateX(0)}
.t-toast.success .t-toast-icon{color:var(--t-success)}
.t-toast.error .t-toast-icon{color:var(--t-danger)}
.t-toast.info .t-toast-icon{color:var(--t-accent)}

/* ── Password modal ── */
.t-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1060;align-items:center;justify-content:center}
.t-modal-overlay.show{display:flex}
.t-modal-box{background:var(--t-bg1);border:1px solid var(--t-border);border-radius:var(--t-radius-lg);padding:28px;width:320px}
.t-modal-title{font-size:16px;font-weight:600;margin-bottom:6px;color:var(--t-text1)}
.t-modal-sub{font-size:13px;color:var(--t-text2);margin-bottom:18px}
.t-modal-input{width:100%;background:var(--t-bg2);border:1px solid var(--t-border);border-radius:var(--t-radius-sm);color:var(--t-text1);font-family:'DM Sans',sans-serif;font-size:14px;padding:10px 12px;outline:none;transition:border .2s;margin-bottom:14px}
.t-modal-input:focus{border-color:var(--t-accent)}
.t-modal-btns{display:flex;gap:8px}
.t-modal-btn{flex:1;padding:10px;border-radius:var(--t-radius-sm);border:1px solid var(--t-border);font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;cursor:pointer;transition:all .2s}
.t-modal-btn.primary{background:var(--t-accent);border-color:var(--t-accent);color:#fff}
.t-modal-btn.primary:hover{opacity:.9}
.t-modal-btn.secondary{background:transparent;color:var(--t-text2)}
.t-modal-btn.secondary:hover{border-color:var(--t-text2);color:var(--t-text1)}

/* ── PDF Preview modal ── */
.t-preview-pages{max-height:340px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;margin-bottom:16px;background:var(--t-bg0);border-radius:var(--t-radius-sm);padding:10px}
</style>
@endpush

@section('content')

{{-- Encabezado de página --}}
<div class="page-header mb-4">
    <div class="page-header-left">
        <div class="page-header-icon blue">
            <i class="bi bi-file-image"></i>
        </div>
        <div>
            <h2>IMG a PDF</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('inicio') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Herramientas</li>
                    <li class="breadcrumb-item active">IMG a PDF</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline-secondary" title="Proteger con contraseña" onclick="tShowPasswordModal()">
            <i class="bi bi-lock"></i> Contraseña PDF
        </button>
        <button class="btn btn-sm btn-outline-secondary" onclick="document.getElementById('tFileInput').click()">
            <i class="bi bi-plus-circle"></i> Agregar imágenes
        </button>
    </div>
</div>

{{-- Herramienta IMG a PDF --}}
<div class="img2pdf-app" id="img2pdfApp">

    {{-- SIDEBAR --}}
    <aside class="t-sidebar">
        <div class="t-sidebar-header">
            <span class="t-sidebar-title">Imágenes</span>
            <span class="t-count-badge" id="tCountBadge">0</span>
        </div>
        <div class="t-drop-zone" id="tDropZone" onclick="document.getElementById('tFileInput').click()">
            <div class="t-drop-icon"><i class="ti ti-photo-up"></i></div>
            <div class="t-drop-text"><strong>Arrastra imágenes aquí</strong><br>o haz clic para explorar</div>
            <div class="t-drop-ext">
                <span class="t-ext-pill">JPG</span><span class="t-ext-pill">PNG</span>
                <span class="t-ext-pill">WEBP</span><span class="t-ext-pill">BMP</span>
                <span class="t-ext-pill">GIF</span><span class="t-ext-pill">TIFF</span>
            </div>
        </div>
        <div class="t-thumb-list" id="tThumbList"></div>
        <div class="t-sidebar-footer">
            <button class="t-clear-btn" onclick="tClearAll()">
                <i class="ti ti-trash" style="font-size:13px"></i> Eliminar todas
            </button>
        </div>
    </aside>

    {{-- MAIN PREVIEW --}}
    <main class="t-main">
        <div class="t-preview-wrap" id="tPreviewWrap">
            <div class="t-empty-state" id="tEmptyState">
                <div class="t-empty-icon"><i class="ti ti-photo"></i></div>
                <div class="t-empty-title">Sin imágenes aún</div>
                <div class="t-empty-sub">Importa imágenes desde la barra lateral<br>o arrástralas directamente aquí</div>
            </div>
            <div class="t-preview-img-wrap" id="tPreviewImgWrap" style="display:none">
                <img id="tPreviewImg" class="t-preview-img" src="" alt="Vista previa" style="max-height:380px">
                <div class="t-preview-toolbar">
                    <button class="t-pt-btn" onclick="tRotateCurrent(-90)"><i class="ti ti-rotate-counterclockwise"></i> -90°</button>
                    <button class="t-pt-btn" onclick="tRotateCurrent(90)"><i class="ti ti-rotate-clockwise"></i> +90°</button>
                    <button class="t-pt-btn" onclick="tPrevImg()"><i class="ti ti-chevron-left"></i></button>
                    <button class="t-pt-btn" onclick="tNextImg()"><i class="ti ti-chevron-right"></i></button>
                    <button class="t-pt-btn danger" onclick="tRemoveCurrent()"><i class="ti ti-trash"></i></button>
                </div>
            </div>
        </div>
        <div class="t-progress-section" id="tProgressSection" style="display:none">
            <div class="t-progress-bar-wrap"><div class="t-progress-bar" id="tProgressBar"></div></div>
            <div class="t-progress-text" id="tProgressText">Procesando...</div>
        </div>
    </main>

    {{-- RIGHT PANEL --}}
    <div class="t-panel">
        {{-- Configuración de página --}}
        <div class="t-panel-section">
            <div class="t-panel-label">Configuración de página</div>
            <div class="t-row2" style="margin-bottom:10px">
                <div class="t-field-group">
                    <span class="t-field-label">Tamaño de hoja</span>
                    <select class="t-field-select" id="tPageSize">
                        <option value="letter">Carta</option>
                        <option value="legal">Oficio</option>
                        <option value="a4" selected>A4</option>
                        <option value="custom">Personalizado</option>
                    </select>
                </div>
                <div class="t-field-group">
                    <span class="t-field-label">Orientación</span>
                    <select class="t-field-select" id="tOrientation">
                        <option value="portrait">Vertical</option>
                        <option value="landscape">Horizontal</option>
                    </select>
                </div>
            </div>
            <div class="t-row2" id="tCustomSizeRow" style="display:none;margin-bottom:10px">
                <div class="t-field-group">
                    <span class="t-field-label">Ancho (mm)</span>
                    <input type="number" class="t-field-input" id="tCustomW" value="210" min="50" max="1000">
                </div>
                <div class="t-field-group">
                    <span class="t-field-label">Alto (mm)</span>
                    <input type="number" class="t-field-input" id="tCustomH" value="297" min="50" max="1000">
                </div>
            </div>
            <div class="t-field-group">
                <span class="t-field-label">Márgenes (mm)</span>
                <div class="t-slider-wrap">
                    <input type="range" class="t-slider" id="tMargin" min="0" max="40" value="10" step="1"
                           oninput="document.getElementById('tMarginVal').textContent=this.value+'mm'">
                    <span class="t-slider-val" id="tMarginVal">10mm</span>
                </div>
            </div>
        </div>

        {{-- Calidad y opciones --}}
        <div class="t-panel-section">
            <div class="t-panel-label">Calidad y opciones</div>
            <div class="t-field-group" style="margin-bottom:10px">
                <span class="t-field-label">Calidad de imagen</span>
                <div class="t-slider-wrap">
                    <input type="range" class="t-slider" id="tQuality" min="10" max="100" value="90" step="5"
                           oninput="document.getElementById('tQualVal').textContent=this.value+'%';tUpdateEstimate()">
                    <span class="t-slider-val" id="tQualVal">90%</span>
                </div>
            </div>
            <div class="t-toggle-row">
                <span class="t-toggle-label">Comprimir PDF</span>
                <label class="t-toggle">
                    <input type="checkbox" id="tCompress" onchange="tUpdateEstimate()">
                    <div class="t-toggle-track"></div><div class="t-toggle-thumb"></div>
                </label>
            </div>
            <div class="t-toggle-row">
                <span class="t-toggle-label">Una imagen por página</span>
                <label class="t-toggle">
                    <input type="checkbox" id="tSeparatePages" checked>
                    <div class="t-toggle-track"></div><div class="t-toggle-thumb"></div>
                </label>
            </div>
            <div class="t-toggle-row">
                <span class="t-toggle-label">Optimizar imágenes</span>
                <label class="t-toggle">
                    <input type="checkbox" id="tOptimize" checked>
                    <div class="t-toggle-track"></div><div class="t-toggle-thumb"></div>
                </label>
            </div>
            <div class="t-toggle-row">
                <span class="t-toggle-label">Ajustar a página</span>
                <label class="t-toggle">
                    <input type="checkbox" id="tFitPage" checked>
                    <div class="t-toggle-track"></div><div class="t-toggle-thumb"></div>
                </label>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="t-panel-section">
            <div class="t-panel-label">Estadísticas</div>
            <div class="t-stats-grid">
                <div class="t-stat-card"><div class="t-stat-val" id="tStatCount">0</div><div class="t-stat-lbl">Imágenes</div></div>
                <div class="t-stat-card"><div class="t-stat-val" id="tStatEst">—</div><div class="t-stat-lbl">PDF estimado</div></div>
                <div class="t-stat-card"><div class="t-stat-val" id="tStatPages">0</div><div class="t-stat-lbl">Páginas</div></div>
                <div class="t-stat-card"><div class="t-stat-val" id="tStatSize">0 KB</div><div class="t-stat-lbl">Total fuente</div></div>
            </div>
        </div>

        {{-- Nombre del archivo --}}
        <div class="t-panel-section">
            <div class="t-panel-label">Nombre del archivo</div>
            <input type="text" class="t-field-input" id="tFilename" value="documento" placeholder="Nombre del PDF">
        </div>

        {{-- Sección historial (oculta por defecto) --}}
        <div class="t-panel-section" id="tHistorySection" style="display:none">
            <div class="t-panel-label">Historial</div>
            <div id="tHistoryList"></div>
        </div>

        <div style="display:flex;gap:8px;padding:0 16px 14px;margin-top:-6px">
            <button class="t-convert-btn" id="tPreviewBtn" onclick="tStartConvert(true)" disabled
                    style="flex:0 0 44px;padding:12px;background:var(--t-bg2);border:1px solid var(--t-border);color:var(--t-text2);"
                    title="Vista previa">
                <i class="ti ti-eye" style="font-size:18px"></i>
            </button>
            <button class="t-convert-btn" id="tConvertBtn" onclick="tStartConvert(false)" disabled style="flex:1">
                <i class="ti ti-file-type-pdf" style="font-size:18px"></i> Generar PDF
            </button>
        </div>

        {{-- Tab historial en el panel --}}
        <div style="padding:0 16px 14px;display:flex;gap:4px">
            <button class="t-convert-btn" id="tTabConvert" onclick="tSwitchTab('convert',this)"
                    style="flex:1;padding:8px;font-size:12px;background:var(--t-accent)">
                <i class="ti ti-transform" style="font-size:14px"></i> Convertir
            </button>
            <button class="t-convert-btn" id="tTabHistory" onclick="tSwitchTab('history',this)"
                    style="flex:1;padding:8px;font-size:12px;background:var(--t-bg3);color:var(--t-text2)">
                <i class="ti ti-history" style="font-size:14px"></i> Historial
            </button>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="t-toast-wrap" id="tToastWrap"></div>

{{-- Modal contraseña --}}
<div class="t-modal-overlay" id="tPwdModal">
    <div class="t-modal-box">
        <div class="t-modal-title"><i class="ti ti-lock" style="font-size:18px;vertical-align:-2px;margin-right:6px"></i>Proteger con contraseña</div>
        <div class="t-modal-sub">Nota: la protección con contraseña en PDFs generados en el navegador es informativa. Para cifrado real usa una herramienta de escritorio.</div>
        <input type="password" class="t-modal-input" id="tPdfPassword" placeholder="Contraseña (dejar vacío para quitar)">
        <div class="t-modal-btns">
            <button class="t-modal-btn secondary" onclick="tHidePasswordModal()">Cancelar</button>
            <button class="t-modal-btn primary" onclick="tSavePassword()">Guardar</button>
        </div>
    </div>
</div>

{{-- Modal vista previa PDF --}}
<div class="t-modal-overlay" id="tPdfPreviewModal">
    <div class="t-modal-box" style="width:520px;max-width:95vw">
        <div class="t-modal-title"><i class="ti ti-eye" style="font-size:18px;vertical-align:-2px;margin-right:6px"></i>Vista previa del PDF</div>
        <div class="t-modal-sub" id="tPreviewModalSub">Revisando el documento antes de descargar.</div>
        <div id="tPdfPreviewPages" class="t-preview-pages"></div>
        <div class="t-modal-btns">
            <button class="t-modal-btn secondary" onclick="document.getElementById('tPdfPreviewModal').classList.remove('show')">Cancelar</button>
            <button class="t-modal-btn primary" id="tPreviewDownloadBtn">
                <i class="ti ti-download" style="font-size:14px;vertical-align:-2px;margin-right:4px"></i>Descargar PDF
            </button>
        </div>
    </div>
</div>

<input type="file" id="tFileInput" accept=".jpg,.jpeg,.png,.webp,.bmp,.gif,.tiff,.tif" multiple
       onchange="tHandleFiles(this.files)" style="display:none">

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function() {
'use strict';

const {jsPDF} = window.jspdf || {};
let tImages = [], tSelectedIdx = 0, tActiveTab = 'convert', tPdfPassword = '', tHistory = [];
const tPageSizes = {letter:[215.9,279.4], legal:[215.9,355.6], a4:[210,297]};

/* ── Tabs ── */
function tSwitchTab(tab, btn) {
    tActiveTab = tab;
    const hs = document.getElementById('tHistorySection');
    const tabConvert = document.getElementById('tTabConvert');
    const tabHistory = document.getElementById('tTabHistory');
    const app = document.getElementById('img2pdfApp');
    const accentBg = getComputedStyle(app).getPropertyValue('--t-accent').trim();
    const bg3 = getComputedStyle(app).getPropertyValue('--t-bg3').trim();
    const text2 = getComputedStyle(app).getPropertyValue('--t-text2').trim();

    if (tab === 'history') {
        hs.style.display = 'block';
        tRenderHistory();
        tabHistory.style.background = accentBg;
        tabHistory.style.color = '#fff';
        tabConvert.style.background = bg3;
        tabConvert.style.color = text2;
    } else {
        hs.style.display = 'none';
        tabConvert.style.background = accentBg;
        tabConvert.style.color = '#fff';
        tabHistory.style.background = bg3;
        tabHistory.style.color = text2;
    }
}

/* ── Contraseña ── */
function tShowPasswordModal() { document.getElementById('tPwdModal').classList.add('show'); }
function tHidePasswordModal() { document.getElementById('tPwdModal').classList.remove('show'); }
function tSavePassword() {
    tPdfPassword = document.getElementById('tPdfPassword').value;
    tHidePasswordModal();
    tShowToast(tPdfPassword ? 'Contraseña registrada' : 'Protección eliminada', tPdfPassword ? 'info' : 'success');
}

/* ── Manejo de archivos ── */
function tHandleFiles(files) {
    const validExts = ['jpg','jpeg','png','webp','bmp','gif','tiff','tif'];
    let loaded = 0, total = files.length;
    Array.from(files).forEach(function(file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (!validExts.includes(ext)) { tShowToast(file.name + ' no es soportado', 'error'); total--; return; }
        const reader = new FileReader();
        reader.onload = function(e) {
            tImages.push({name: file.name, data: e.target.result, size: file.size, rotation: 0, file: file});
            loaded++;
            if (loaded === total) {
                tUpdateUI();
                if (tImages.length > 0) {
                    tSelectImage(tImages.length - 1);
                    tShowToast(total + ' imagen' + (total > 1 ? 'es' : '') + ' añadida' + (total > 1 ? 's' : '') + ' ✓', 'success');
                }
            }
        };
        reader.readAsDataURL(file);
    });
}

function tUpdateUI() {
    const n = tImages.length;
    document.getElementById('tCountBadge').textContent = n;
    document.getElementById('tStatCount').textContent = n;
    document.getElementById('tStatPages').textContent = n;
    document.getElementById('tConvertBtn').disabled = n === 0;
    document.getElementById('tPreviewBtn').disabled = n === 0;
    tRenderThumbs();
    tUpdateEstimate();
    if (n === 0) {
        document.getElementById('tPreviewImgWrap').style.display = 'none';
        document.getElementById('tEmptyState').style.display = 'block';
        document.getElementById('tStatSize').textContent = '0 KB';
        document.getElementById('tStatEst').textContent = '—';
    } else {
        const totalBytes = tImages.reduce(function(s, i) { return s + i.size; }, 0);
        document.getElementById('tStatSize').textContent = tFormatSize(totalBytes);
    }
}

function tFormatSize(bytes) {
    if (bytes < 1024) return bytes + 'B';
    if (bytes < 1048576) return Math.round(bytes / 1024) + 'KB';
    return (bytes / 1048576).toFixed(1) + 'MB';
}

function tUpdateEstimate() {
    if (tImages.length === 0) { document.getElementById('tStatEst').textContent = '—'; return; }
    const quality = parseInt(document.getElementById('tQuality').value) / 100;
    const compress = document.getElementById('tCompress').checked;
    const total = tImages.reduce(function(s, i) { return s + i.size; }, 0);
    const est = total * quality * (compress ? .6 : 1);
    document.getElementById('tStatEst').textContent = tFormatSize(Math.round(est));
}

function tRenderThumbs() {
    const list = document.getElementById('tThumbList');
    list.innerHTML = '';
    tImages.forEach(function(img, i) {
        const div = document.createElement('div');
        div.className = 't-thumb-item' + (i === tSelectedIdx ? ' selected' : '');
        div.draggable = true;
        div.dataset.idx = i;
        div.innerHTML = [
            '<span class="t-thumb-idx">' + (i + 1) + '</span>',
            '<img class="t-thumb-preview" src="' + img.data + '" alt="' + img.name + '" style="transform:rotate(' + img.rotation + 'deg)">',
            '<div class="t-thumb-info">',
            '  <div class="t-thumb-name">' + img.name + '</div>',
            '  <div class="t-thumb-size">' + tFormatSize(img.size) + '</div>',
            '</div>',
            '<div class="t-thumb-actions">',
            '  <button class="t-thumb-act-btn" onclick="tMoveUp(' + i + ',event)" title="Subir"><i class="ti ti-arrow-up" style="font-size:10px"></i></button>',
            '  <button class="t-thumb-act-btn" onclick="tMoveDown(' + i + ',event)" title="Bajar"><i class="ti ti-arrow-down" style="font-size:10px"></i></button>',
            '  <button class="t-thumb-act-btn del" onclick="tRemoveImage(' + i + ',event)" title="Eliminar"><i class="ti ti-trash" style="font-size:10px"></i></button>',
            '</div>'
        ].join('');
        div.addEventListener('click', function() { tSelectImage(i); });
        div.addEventListener('dragstart', function(e) {
            tDraggingThumb = true;
            e.dataTransfer.setData('text/plain', i);
            div.classList.add('dragging');
        });
        div.addEventListener('dragend', function() { tDraggingThumb = false; div.classList.remove('dragging'); });
        div.addEventListener('dragover', function(e) { e.preventDefault(); });
        div.addEventListener('drop', function(e) {
            e.preventDefault();
            const from = parseInt(e.dataTransfer.getData('text/plain'));
            tSwapImages(from, i);
        });
        list.appendChild(div);
    });
}

function tSelectImage(i) {
    tSelectedIdx = i;
    if (tImages[i]) {
        document.getElementById('tEmptyState').style.display = 'none';
        document.getElementById('tPreviewImgWrap').style.display = 'block';
        const img = document.getElementById('tPreviewImg');
        img.src = tImages[i].data;
        img.style.transform = 'rotate(' + tImages[i].rotation + 'deg)';
    }
    tRenderThumbs();
}

function tRotateCurrent(deg) {
    if (!tImages[tSelectedIdx]) return;
    tImages[tSelectedIdx].rotation = (tImages[tSelectedIdx].rotation + deg + 360) % 360;
    tSelectImage(tSelectedIdx);
}
function tPrevImg() { if (tSelectedIdx > 0) tSelectImage(tSelectedIdx - 1); }
function tNextImg() { if (tSelectedIdx < tImages.length - 1) tSelectImage(tSelectedIdx + 1); }
function tRemoveCurrent() { if (tImages[tSelectedIdx]) tRemoveImage(tSelectedIdx, new Event('')); }

function tRemoveImage(i, e) {
    e.stopPropagation();
    tImages.splice(i, 1);
    if (tSelectedIdx >= tImages.length) tSelectedIdx = Math.max(0, tImages.length - 1);
    tUpdateUI();
    if (tImages.length > 0) tSelectImage(tSelectedIdx);
}

function tMoveUp(i, e) {
    e.stopPropagation();
    if (i > 0) {
        var tmp = tImages[i-1]; tImages[i-1] = tImages[i]; tImages[i] = tmp;
        tSelectedIdx = i - 1; tUpdateUI(); tSelectImage(tSelectedIdx);
    }
}
function tMoveDown(i, e) {
    e.stopPropagation();
    if (i < tImages.length - 1) {
        var tmp = tImages[i]; tImages[i] = tImages[i+1]; tImages[i+1] = tmp;
        tSelectedIdx = i + 1; tUpdateUI(); tSelectImage(tSelectedIdx);
    }
}
function tSwapImages(a, b) {
    if (a === b) return;
    var tmp = tImages[a]; tImages[a] = tImages[b]; tImages[b] = tmp;
    tSelectedIdx = b; tUpdateUI(); tSelectImage(tSelectedIdx);
}
function tClearAll() {
    if (tImages.length === 0) return;
    tImages = []; tSelectedIdx = 0; tUpdateUI();
    tShowToast('Todas las imágenes eliminadas', 'info');
}

/* ── Drag & drop zona ── */
var tDraggingThumb = false;
var tDz = document.getElementById('tDropZone');

document.getElementById('img2pdfApp').addEventListener('dragover', function(e) {
    e.preventDefault();
    if (!tDraggingThumb) tDz.classList.add('drag-over');
});
document.getElementById('img2pdfApp').addEventListener('dragleave', function(e) {
    if (!e.relatedTarget || !document.getElementById('img2pdfApp').contains(e.relatedTarget))
        tDz.classList.remove('drag-over');
});
document.getElementById('img2pdfApp').addEventListener('drop', function(e) {
    e.preventDefault();
    tDz.classList.remove('drag-over');
    if (!tDraggingThumb && e.dataTransfer.files.length) tHandleFiles(e.dataTransfer.files);
});

/* ── Tamaño personalizado ── */
document.getElementById('tPageSize').addEventListener('change', function() {
    document.getElementById('tCustomSizeRow').style.display = this.value === 'custom' ? 'grid' : 'none';
});

/* ── Toast ── */
function tShowToast(msg, type) {
    type = type || 'info';
    const wrap = document.getElementById('tToastWrap');
    const t = document.createElement('div');
    const icons = {success:'ti-check', error:'ti-x', info:'ti-info-circle'};
    t.className = 't-toast ' + type;
    t.innerHTML = '<i class="ti ' + (icons[type] || 'ti-info-circle') + ' t-toast-icon" style="font-size:16px"></i>' + msg;
    wrap.appendChild(t);
    setTimeout(function() { t.classList.add('show'); }, 20);
    setTimeout(function() { t.classList.remove('show'); setTimeout(function() { t.remove(); }, 400); }, 3200);
}

/* ── Progreso ── */
function tSetProgress(pct, text) {
    const ps = document.getElementById('tProgressSection');
    if (pct < 0) { ps.style.display = 'none'; return; }
    ps.style.display = 'block';
    document.getElementById('tProgressBar').style.width = pct + '%';
    document.getElementById('tProgressText').textContent = text || (Math.round(pct) + '%');
}

/* ── Conversión ── */
async function tStartConvert(previewOnly) {
    previewOnly = previewOnly || false;
    if (!tImages.length) return;
    if (!jsPDF) { tShowToast('Motor PDF no disponible', 'error'); return; }

    const btnId = previewOnly ? 'tPreviewBtn' : 'tConvertBtn';
    const btn = document.getElementById(btnId);
    const origContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = previewOnly
        ? '<i class="ti ti-loader" style="font-size:18px;animation:tSpin 1s linear infinite"></i>'
        : '<i class="ti ti-loader" style="font-size:18px;animation:tSpin 1s linear infinite"></i> Generando...';

    if (!document.getElementById('tSpinStyle')) {
        const s = document.createElement('style');
        s.id = 'tSpinStyle';
        s.textContent = '@keyframes tSpin{from{transform:rotate(0)}to{transform:rotate(360deg)}}';
        document.head.appendChild(s);
    }

    tSetProgress(0, 'Iniciando conversión...');

    try {
        const size = document.getElementById('tPageSize').value;
        const orient = document.getElementById('tOrientation').value;
        const margin = parseInt(document.getElementById('tMargin').value);
        const quality = parseInt(document.getElementById('tQuality').value) / 100;
        const fitPage = document.getElementById('tFitPage').checked;
        const optimize = document.getElementById('tOptimize').checked;
        const separatePages = document.getElementById('tSeparatePages').checked;
        const fname = (document.getElementById('tFilename').value || 'documento').replace(/[^a-z0-9_\-]/gi, '_');

        let pageW, pageH;
        if (size === 'custom') {
            pageW = parseFloat(document.getElementById('tCustomW').value);
            pageH = parseFloat(document.getElementById('tCustomH').value);
        } else {
            var ps = tPageSizes[size] || tPageSizes.a4;
            pageW = ps[0]; pageH = ps[1];
        }

        const doc = new jsPDF({orientation: orient, unit: 'mm', format: size === 'custom' ? [pageW, pageH] : size});
        const pW = orient === 'portrait' ? pageW : pageH;
        const pH = orient === 'portrait' ? pageH : pageW;
        const aW = pW - margin * 2, aH = pH - margin * 2;

        for (let i = 0; i < tImages.length; i++) {
            tSetProgress((i / tImages.length) * 90, 'Procesando imagen ' + (i + 1) + ' de ' + tImages.length + '...');
            await new Promise(function(r) { setTimeout(r, 30); });
            if (i > 0 && separatePages) doc.addPage(size === 'custom' ? [pageW, pageH] : size, orient);

            const img = tImages[i];
            const imgEl = await tLoadImageElement(img.data);
            let iW = imgEl.naturalWidth, iH = imgEl.naturalHeight;
            if (img.rotation === 90 || img.rotation === 270) { var tmp = iW; iW = iH; iH = tmp; }
            let drawW = aW, drawH = iH * (aW / iW);
            if (fitPage && drawH > aH) { drawH = aH; drawW = iW * (aH / iH); }
            const x = margin + (aW - drawW) / 2, y = margin + (aH - drawH) / 2;

            const canvas = document.createElement('canvas');
            const scale = optimize ? Math.min(1, 1200 / Math.max(imgEl.naturalWidth, imgEl.naturalHeight)) : 1;
            canvas.width = Math.round(imgEl.naturalWidth * scale);
            canvas.height = Math.round(imgEl.naturalHeight * scale);
            const ctx = canvas.getContext('2d');
            if (img.rotation) {
                ctx.translate(canvas.width / 2, canvas.height / 2);
                ctx.rotate(img.rotation * Math.PI / 180);
                ctx.drawImage(imgEl, -canvas.width / 2, -canvas.height / 2, canvas.width, canvas.height);
            } else {
                ctx.drawImage(imgEl, 0, 0, canvas.width, canvas.height);
            }
            const dataUrl = canvas.toDataURL('image/jpeg', quality);
            const fmt = img.name.toLowerCase().match(/\.png$/) ? 'PNG' : 'JPEG';
            doc.addImage(dataUrl, fmt, x, y, drawW, drawH, '', document.getElementById('tCompress').checked ? 'FAST' : 'NONE');
        }

        tSetProgress(95, 'Finalizando PDF...');
        await new Promise(function(r) { setTimeout(r, 50); });

        const pdfBlob = doc.output('blob');
        const finalSize = pdfBlob.size;
        tSetProgress(100, previewOnly ? 'Vista previa lista' : '¡PDF generado!');

        if (previewOnly) {
            const container = document.getElementById('tPdfPreviewPages');
            container.innerHTML = '';
            document.getElementById('tPreviewModalSub').textContent =
                tImages.length + ' página' + (tImages.length > 1 ? 's' : '') + ' · ' + tFormatSize(finalSize) + ' estimado';

            for (let i = 0; i < tImages.length; i++) {
                const img = tImages[i];
                const imgEl = await tLoadImageElement(img.data);
                const cvs = document.createElement('canvas');
                const maxW = 460;
                const ratio = imgEl.naturalHeight / imgEl.naturalWidth;
                cvs.width = maxW; cvs.height = Math.round(maxW * ratio);
                cvs.style.cssText = 'width:100%;border-radius:6px;box-shadow:0 2px 12px rgba(0,0,0,.4);display:block';
                const ctx2 = cvs.getContext('2d');
                if (img.rotation) {
                    ctx2.translate(cvs.width / 2, cvs.height / 2);
                    ctx2.rotate(img.rotation * Math.PI / 180);
                    ctx2.drawImage(imgEl, -cvs.width / 2, -cvs.height / 2, cvs.width, cvs.height);
                } else {
                    ctx2.drawImage(imgEl, 0, 0, cvs.width, cvs.height);
                }
                const label = document.createElement('div');
                label.style.cssText = 'font-size:10px;color:var(--t-text3);text-align:center;margin-top:4px;font-family:Space Mono,monospace';
                label.textContent = 'Página ' + (i + 1) + ' · ' + img.name;
                const wrap = document.createElement('div');
                wrap.appendChild(cvs); wrap.appendChild(label);
                container.appendChild(wrap);
            }

            document.getElementById('tPreviewDownloadBtn').onclick = function() {
                document.getElementById('tPdfPreviewModal').classList.remove('show');
                const url = URL.createObjectURL(pdfBlob);
                const a = document.createElement('a'); a.href = url; a.download = fname + '.pdf'; a.click();
                setTimeout(function() { URL.revokeObjectURL(url); }, 5000);
                tHistory.unshift({name: fname + '.pdf', pages: tImages.length, size: finalSize, date: new Date(), blob: pdfBlob});
                if (tHistory.length > 10) tHistory.pop();
                tShowToast('PDF descargado: ' + tFormatSize(finalSize), 'success');
                document.getElementById('tStatEst').textContent = tFormatSize(finalSize);
            };
            document.getElementById('tPdfPreviewModal').classList.add('show');
            setTimeout(function() { tSetProgress(-1); }, 1500);
        } else {
            tHistory.unshift({name: fname + '.pdf', pages: tImages.length, size: finalSize, date: new Date(), blob: pdfBlob});
            if (tHistory.length > 10) tHistory.pop();
            const url = URL.createObjectURL(pdfBlob);
            const a = document.createElement('a'); a.href = url; a.download = fname + '.pdf'; a.click();
            setTimeout(function() { URL.revokeObjectURL(url); }, 5000);
            tShowToast('PDF generado: ' + tFormatSize(finalSize), 'success');
            document.getElementById('tStatEst').textContent = tFormatSize(finalSize);
            setTimeout(function() { tSetProgress(-1); }, 2500);
        }
    } catch (err) {
        console.error(err);
        tShowToast('Error al generar PDF', 'error');
        tSetProgress(-1);
    }

    btn.disabled = false;
    btn.innerHTML = origContent;
}

function tLoadImageElement(src) {
    return new Promise(function(res, rej) {
        const i = new Image(); i.onload = function() { res(i); }; i.onerror = rej; i.src = src;
    });
}

function tRenderHistory() {
    const list = document.getElementById('tHistoryList');
    if (!tHistory.length) {
        list.innerHTML = '<div style="text-align:center;color:var(--t-text3);font-size:12px;padding:20px 0">Sin conversiones aún</div>';
        return;
    }
    list.innerHTML = tHistory.map(function(h, i) {
        return [
            '<div class="t-history-item">',
            '  <div class="t-history-icon"><i class="ti ti-file-type-pdf"></i></div>',
            '  <div class="t-history-info">',
            '    <div class="t-history-name">' + h.name + '</div>',
            '    <div class="t-history-meta">' + h.pages + ' pág · ' + tFormatSize(h.size) + ' · ' + h.date.toLocaleTimeString('es-MX',{hour:'2-digit',minute:'2-digit'}) + '</div>',
            '  </div>',
            '  <button class="t-history-dl" onclick="tRedownload(' + i + ')" title="Descargar"><i class="ti ti-download" style="font-size:13px"></i></button>',
            '</div>'
        ].join('');
    }).join('');
}

function tRedownload(i) {
    const h = tHistory[i];
    const url = URL.createObjectURL(h.blob);
    const a = document.createElement('a'); a.href = url; a.download = h.name; a.click();
    setTimeout(function() { URL.revokeObjectURL(url); }, 3000);
    tShowToast('Descargando ' + h.name, 'info');
}

/* Exponer funciones globales necesarias para onclick= */
window.tShowPasswordModal  = tShowPasswordModal;
window.tHidePasswordModal  = tHidePasswordModal;
window.tSavePassword       = tSavePassword;
window.tHandleFiles        = tHandleFiles;
window.tClearAll           = tClearAll;
window.tSelectImage        = tSelectImage;
window.tRotateCurrent      = tRotateCurrent;
window.tPrevImg            = tPrevImg;
window.tNextImg            = tNextImg;
window.tRemoveCurrent      = tRemoveCurrent;
window.tRemoveImage        = tRemoveImage;
window.tMoveUp             = tMoveUp;
window.tMoveDown           = tMoveDown;
window.tStartConvert       = tStartConvert;
window.tSwitchTab          = tSwitchTab;
window.tRedownload         = tRedownload;

})();
</script>
@endsection
