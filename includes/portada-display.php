<?php

/**
 * Marco fijo: misma caja para todas las portadas, imagen completa sin recortar (letterbox).
 */
function cssPortadasContenedor() {
    return '
    .portada-frame {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(145deg, #e8eaed 0%, #dfe2e6 100%);
        overflow: hidden;
        flex-shrink: 0;
    }
    .portada-frame img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        display: block;
    }
    .portada-frame--card { width: 100%; aspect-ratio: 3/4; border-radius: 12px 12px 0 0; }
    .portada-frame--shelf { width: 100%; aspect-ratio: 3/4; }
    .portada-frame--hero { width: 140px; aspect-ratio: 3/4; border-radius: 12px; }
    .portada-frame--thumb { width: 96px; min-height: 128px; min-width: 96px; border-radius: 0; }
    .portada-frame--edit { width: 100%; aspect-ratio: 3/4; border-radius: 14px; }
    .portada-frame--version { width: 52px; min-width: 52px; height: 68px; border-radius: 8px; }
    .portada-frame--empty { font-size: 1.75rem; color: #9ca3af; }
    .portada-frame--hero.portada-frame--empty { font-size: 2.5rem; }
    ';
}

/**
 * @param string|null $archivo Nombre de archivo en img/portadas
 * @param string $variante Clase CSS: card, shelf, hero, thumb, edit, version
 * @param string $alt Texto alternativo
 */
function htmlPortada($archivo, $variante = 'card', $alt = '') {
    $clase = 'portada-frame portada-frame--' . preg_replace('/[^a-z]/', '', $variante);
    $base = '../../img/portadas/';
    if ($archivo !== null && $archivo !== '') {
        $src = $base . htmlspecialchars(basename($archivo), ENT_QUOTES);
        $altAttr = $alt !== '' ? ' alt="' . htmlspecialchars($alt, ENT_QUOTES) . '"' : ' alt=""';
        return '<div class="' . $clase . '"><img src="' . $src . '"' . $altAttr . '></div>';
    }
    return '<div class="' . $clase . ' portada-frame--empty" aria-hidden="true"><span>🎮</span></div>';
}
