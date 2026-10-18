<?php
/**
 * The whole site is a single page with its own #/ routes (grid, list, gallery,
 * about, artwork, news, services, contact and cookie pages), so every WordPress URL renders this same document.
 *
 * @package Vangel_Naumovski
 */

$vn_base = trailingslashit( get_template_directory_uri() );
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>48 Art Works</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400&family=IBM+Plex+Mono:wght@400&display=swap" rel="stylesheet">
<style>
  :root {
    --background: #fff;
    --foreground: #171717;
    --ease-out-expo: cubic-bezier(.19, 1, .22, 1);
    --ease-out: cubic-bezier(0, 0, .2, 1);
    --grid-item-size: 5.4375rem;
  }
  @media (min-width: 768px) { :root { --grid-item-size: 7.875rem; } }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; border: 0 solid; }

  /* fluid root size, same breakpoints as the original */
  html { font-size: calc(0.875rem + 0.208333vw); -webkit-font-smoothing: antialiased; height: 100%; }
  @media (max-width: 1440px) { html { font-size: calc(0.968848rem + 0.104058vw); } }
  @media (max-width: 479px)  { html { font-size: calc(0.937369rem + 0.209205vw); } }

  body {
    height: 100%;
    overflow: hidden;
    background: var(--background);
    color: var(--foreground);
    /* DM Mono has no Cyrillic; Macedonian letters fall through to IBM Plex Mono */
    font-family: "DM Mono", "IBM Plex Mono", ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  }
  a { color: inherit; text-decoration: inherit; }
  button, input { font: inherit; color: inherit; letter-spacing: inherit; background: transparent; border-radius: 0; }
  button { cursor: pointer; }
  img { display: block; }
  .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }

  .text-sm { font-size: .8125rem; line-height: 130%; letter-spacing: -0.03em; }
  .uppercase { text-transform: uppercase; }
  .tabular { font-variant-numeric: tabular-nums; }
  .px-global { padding-left: 1rem; padding-right: 1rem; }
  @media (min-width: 96rem) { .px-global { padding-left: 1.5rem; padding-right: 1.5rem; } }
  .md-only { display: none; }
  @media (min-width: 768px) { .md-only { display: inline-block; } .mobile-only { display: none !important; } }

  /* ---------- bracket label with wipe-in fill ---------- */
  .lbl-row { display: flex; align-items: center; gap: .375rem; }
  .lbl-bracket { display: inline-block; text-box: cap alphabetic; }
  .lbl {
    position: relative; display: inline-flex; align-items: center; overflow: hidden;
    padding: .25rem; line-height: 1; letter-spacing: normal;
    transition: color .15s cubic-bezier(.4, 0, .2, 1);
  }
  .lbl > * { position: relative; z-index: 10; text-box: cap alphabetic; }
  .lbl::after {
    content: ""; pointer-events: none; position: absolute; inset: 0; z-index: 0;
    background: #fff; transform: scaleX(0); transform-origin: left;
    transition: transform .3s var(--ease-out-expo);
  }
  .group:hover .lbl::after, .group-item:hover .lbl::after { transform: scaleX(1); }
  .group:hover .lbl, .group-item:hover .lbl { color: #000; }
  .lbl.is-current::after { display: none; }
  .group:hover .lbl.is-current { color: inherit; }
  .lbl.is-current > span { text-decoration: underline; text-underline-offset: 2px; }

  /* labels inside the blended (difference) layers would invert their fill over images,
     so their fill is drawn by an unblended chip on top instead: always solid black */
  #navbar .lbl::after, #search-ui .lbl::after, .lang .lbl::after { display: none; }
  #navbar .group:hover .lbl, #search-ui .group:hover .lbl, .lang .group:hover .lbl { color: inherit; }
  .hover-chip {
    position: fixed; z-index: 10000; pointer-events: none; margin: 0;
    background: #000; color: #fff;
    clip-path: inset(0 100% 0 0);
    transition: clip-path .3s var(--ease-out-expo);
  }
  .hover-chip.is-on { clip-path: inset(0 0 0 0); }
  .hover-chip::after { display: none; }

  /* ---------- header ---------- */
  #navbar {
    position: fixed; top: 0; left: 0; right: 0; z-index: 9999;
    padding-top: 1rem; padding-bottom: 1rem;
    color: var(--background); mix-blend-mode: difference;
    opacity: 0; transition: opacity 1s;
  }
  .ui-visible #navbar { opacity: 1; }
  .nav-grid { display: grid; grid-template-columns: 1fr auto; align-items: center; }
  @media (min-width: 768px) { .nav-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
  .logo { display: inline-block; width: fit-content; transition: all .2s; }
  .logo:active { opacity: .75; }
  .nav-menu { display: none; align-items: center; gap: 1rem; justify-self: center; }
  .nav-end { display: none; justify-self: end; }
  @media (min-width: 768px) { .nav-menu, .nav-end { display: flex; } }
  .menu-btn { justify-self: end; color: #fff; }

  /* ---------- filters / search bar ---------- */
  #search-ui {
    position: fixed; top: 5rem; left: 0; right: 0; z-index: 9998;
    color: var(--background); mix-blend-mode: difference;
    opacity: 0; pointer-events: none; transition: opacity 1s;
  }
  .ui-visible #search-ui { opacity: 1; pointer-events: auto; }
  #search-ui.is-open { color: #fff; mix-blend-mode: normal; }
  .search-col { display: flex; flex-direction: column; gap: 1.25rem; }
  .search-row { display: flex; align-items: center; gap: 1.25rem; }
  .search-field { margin-left: auto; display: flex; min-width: 0; align-items: center; gap: .75rem; }
  .search-field.is-active { border-bottom: 1px solid currentColor; }
  .search-input-wrap { display: flex; min-width: 0; align-items: center; gap: .5rem; overflow: hidden; width: 0; opacity: 0; }
  .search-field.is-active .search-input-wrap { width: 14rem; opacity: 1; }
  @media (min-width: 768px) { .search-field.is-active .search-input-wrap { width: 18rem; } }
  .search-input-wrap input { height: 1.5rem; min-width: 0; flex: 1; appearance: none; outline: none; }
  .search-input-wrap input::-webkit-search-cancel-button { display: none; appearance: none; }
  .clear-btn { flex-shrink: 0; font-size: .65rem; text-transform: uppercase; }
  .cats {
    overflow: hidden; max-height: 0; opacity: 0; pointer-events: none;
    transition: max-height .3s var(--ease-out), opacity .3s var(--ease-out);
  }
  .cats.is-open { max-height: 10rem; opacity: 1; pointer-events: auto; }
  .cats-inner { display: flex; flex-wrap: wrap; column-gap: 1.25rem; row-gap: .75rem; }
  .cat { display: inline-flex; align-items: center; gap: .375rem; text-align: left; text-transform: none; }
  .check svg { display: inline; margin: 0 .125rem; width: .25em; height: .25em; transform: translateY(-1px) scale(0); transition: transform .2s; }
  .cat.is-checked .check svg { transform: translateY(-1px) scale(1); }

  #s-backdrop { position: fixed; inset: 0; z-index: 9996; background: #111; opacity: 0; visibility: hidden; }
  #s-results {
    position: fixed; inset: 0; z-index: 9997; overflow-y: auto; scrollbar-width: none;
    padding-top: 12rem; padding-bottom: 2.5rem; color: #fff; opacity: 0; visibility: hidden;
  }
  #s-results::-webkit-scrollbar { display: none; }
  #s-results.filters-open { padding-top: 16rem; }
  .s-list { list-style: none; display: flex; flex-direction: column; gap: 1.5rem; }
  @media (min-width: 768px) { .s-list { gap: 2rem; } }
  .s-item { display: flex; align-items: center; gap: 1rem; transition: opacity .15s; }
  .s-list:hover .s-item { opacity: .4; }
  .s-list .s-item:hover { opacity: 1; }
  .s-thumb { position: relative; width: 6.5rem; height: 6.5rem; flex-shrink: 0; overflow: hidden; }
  .s-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .s-text { min-width: 0; flex: 1; font-size: 1rem; line-height: 130%; letter-spacing: -0.05em; }
  .s-name { color: #fafafa; }
  .s-more { margin-left: auto; display: none; flex-shrink: 0; }
  @media (min-width: 768px) { .s-more { display: inline-flex; } }
  .s-empty { display: flex; min-height: 50dvh; flex-direction: column; align-items: center; justify-content: center; gap: 1.5rem; text-align: center; }
  .s-empty p { font-size: 1rem; color: #acacac; }

  /* ---------- language switcher ---------- */
  .lang {
    position: fixed; right: 1rem; bottom: 1.25rem; z-index: 9000;
    display: flex; align-items: center; gap: .5rem;
    color: var(--background); mix-blend-mode: difference;
  }
  @media (min-width: 96rem) { .lang { right: 1.5rem; } }
  .lang a { display: inline-flex; }

  /* ---------- infinite grid ---------- */
  #content { position: fixed; inset: 0; touch-action: none; overflow: hidden; user-select: none; -webkit-user-select: none; }
  #content.is-invisible { visibility: hidden; }
  #content.no-pointer { pointer-events: none; }
  #draggable { position: relative; width: 100%; height: 100%; }
  .gi {
    position: absolute; top: 0; left: 0; display: block;
    width: var(--grid-item-size);
    transform: translate3d(-200vw, -200vh, 0);
    -webkit-user-drag: none;
  }
  .card { transition: transform .5s var(--ease-out); }
  @media (min-width: 768px) {
    .gi:hover .card { transform: scale(1.08); }
    .gi.is-fill:hover .card { transform: scale(1.035); }
  }
  .cap {
    margin-bottom: .25rem; width: 100%; overflow: hidden;
    font-size: 10px; line-height: 1; white-space: nowrap;
    opacity: 0; visibility: hidden; transition: opacity 1.6s;
  }
  .is-fill .cap { margin-bottom: .5rem; }
  @media (min-width: 768px) { .cap { margin-bottom: .5rem; } .is-fill .cap { margin-bottom: 1rem; } }
  @media (min-width: 1024px) { .cap { font-size: 11px; } }
  #content.captions-ready .cap { opacity: 1 !important; visibility: visible !important; }
  .cap-line { display: flex; min-width: 0; align-items: center; gap: .5rem; }
  .cap-num { display: none; flex-shrink: 0; }
  .cap-name { display: none; min-width: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: right; }
  .is-fill .cap-num, .is-fill .cap-name { display: inline-block; }
  @media (min-width: 768px) { .cap-num, .cap-name { display: inline-block; } }
  .media { position: relative; overflow: hidden; width: var(--grid-item-size); height: var(--grid-item-size); }
  .media-inner { width: 100%; height: 100%; overflow: hidden; }
  .media img { width: 100%; height: 100%; object-fit: cover; pointer-events: none; }

  /* ---------- mobile menu ---------- */
  /* ---------- list view (/archive) ---------- */
  :root { --ease: cubic-bezier(.645,.045,.355,1); --ease-in: cubic-bezier(.4,0,1,1); --ease-in-out: cubic-bezier(.4,0,.2,1); --ease-magic: cubic-bezier(.33,0,0,1); }
  ::view-transition-old(root) { animation: vt-fade-out .2s var(--ease) forwards; }
  ::view-transition-new(root) { animation: vt-fade-in .2s var(--ease) forwards; }
  @keyframes vt-fade-out { to { opacity: 0; } }
  @keyframes vt-fade-in { from { opacity: 0; } }

  html.lenis, html.lenis body { height: auto; }
  .lenis:not(.lenis-autoToggle).lenis-stopped { overflow: clip; }
  .lenis.lenis-smooth iframe { pointer-events: none; }
  body.is-list { height: auto; overflow: visible; }
  body.is-list #content, body.is-gallery #content { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
  body.is-list #search-ui, body.is-list #s-backdrop, body.is-list #s-results,
  body.is-gallery #search-ui, body.is-gallery #s-backdrop, body.is-gallery #s-results { display: none !important; }


  /* ---------- about view (/about) ---------- */
  body.is-about #content { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
  body.is-about #search-ui, body.is-about #s-backdrop, body.is-about #s-results { display: none !important; }
  body.is-about { height: auto; overflow: visible; }
  #about-view { display: none; min-height: 100dvh; flex-direction: column; }
  body.is-about #about-view { display: flex; }
  .about-main { flex: 1; padding-top: 126px; }

  /* desktop: text column in the middle, photos floating around it at different scroll speeds */
  .ab-desk { position: relative; display: none; min-height: 2750px; }
  @media (min-width: 1024px) { .ab-desk { display: block; } }
  .ab-fig { position: absolute; }
  .ab-fig .reveal-image { width: 100%; height: 100%; }
  .ab-article {
    position: absolute; top: 129px; left: 50%; width: 352px; transform: translateX(-50%);
    animation: ab-fade-in 1s var(--ease) both;
  }
  @keyframes ab-fade-in { from { opacity: 0; } }
  .ab-title { margin-bottom: 2.5rem; font-size: .8125rem; font-weight: 400; }
  .ab-paras > p + p { margin-top: 1.75rem; }

  /* mobile: text and photos stacked */
  .ab-mobile { display: flex; flex-direction: column; gap: 2rem; padding-bottom: 3rem; }
  @media (min-width: 1024px) { .ab-mobile { display: none; } }
  .ab-mobile .ab-m-img { position: relative; width: 100%; }
  .ab-mobile .ab-m-img.is-half { max-width: 50%; align-self: flex-end; }

  /* image reveal: wiped in from the bottom while settling from 120% */
  .reveal-image { overflow: hidden; contain: paint; visibility: hidden; }
  .reveal-image.is-inview.is-loaded { visibility: visible; }
  .reveal-image img {
    display: block; width: 100%; height: 100%; object-fit: cover;
    clip-path: inset(100% 0 0); transform: scale(1.2);
    transition: clip-path 1s var(--ease-magic), transform 1s var(--ease-magic);
  }
  .reveal-image.is-inview.is-loaded img { clip-path: inset(0); transform: scale(1); }

  /* text "paints" from light grey to black as it scrolls up through the screen */
  .text-paint {
    --paint: 0%;
    color: transparent;
    background-image: linear-gradient(to bottom, #000 calc(var(--paint) - .4em), rgba(0, 0, 0, .3) calc(var(--paint) + .4em));
    -webkit-background-clip: text; background-clip: text;
  }
  @media (prefers-reduced-motion: reduce) {
    .text-paint { --paint: 100%; }
    .reveal-image img { clip-path: inset(0); transition: none; transform: scale(1); }
  }

  /* artworks table */
  .ab-works { padding-top: 1.5rem; font-size: .813rem; line-height: 1.056rem; letter-spacing: -0.041rem; }
  .ab-works-head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; column-gap: 1.5rem; row-gap: .75rem; }
  .ab-works-search { margin-left: auto; width: 100%; }
  @media (min-width: 640px) { .ab-works-search { width: auto; } }
  .ab-works-search .lb-input-wrap input::placeholder { color: color-mix(in srgb, currentColor 35%, transparent); }
  .ab-rule { margin-top: .5rem; border-top: 1px solid rgba(0, 0, 0, .3); }
  .ab-list { list-style: none; position: relative; left: 50%; width: 100vw; transform: translateX(-50%); margin-top: 1.25rem; font-size: .875rem; }
  .ab-item { position: relative; }
  .ab-item-bg { position: absolute; inset: 0; background: var(--foreground); opacity: 0; pointer-events: none; }
  .ab-item:hover .ab-item-bg { opacity: 1; }
  .ab-row {
    position: relative; display: grid; grid-template-columns: minmax(0, 1fr) auto; grid-template-rows: auto auto;
    column-gap: 2rem; row-gap: .25rem; align-items: start; padding-top: .25rem; padding-bottom: .25rem;
    font-size: .8125rem; line-height: 130%; letter-spacing: -0.03em; color: var(--foreground);
  }
  .ab-item:hover .ab-row, .ab-item:hover .ab-meta { color: var(--background); }
  .ab-name { grid-column: 1; grid-row: 1; min-width: 0; }
  .ab-meta { grid-column: 1; grid-row: 2; min-width: 0; overflow-wrap: break-word; color: #a1a1a1; }
  .ab-discover { grid-column: 2; grid-row: 1 / span 2; align-self: center; justify-self: end; white-space: nowrap; }
  @media (min-width: 640px) {
    .ab-row { grid-template-columns: minmax(0, 22rem) minmax(0, 1fr) auto; grid-template-rows: auto; align-items: center; row-gap: 0; }
    .ab-meta { grid-column: 2; grid-row: 1; }
    .ab-discover { grid-column: 3; grid-row: 1; }
  }
  .ab-empty { margin-top: 1.25rem; color: rgba(23, 23, 23, .5); }
  .ab-empty em { font-weight: 700; }


  /* ---------- artwork page (/artwork/N) ---------- */
  body.is-artwork #content { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
  body.is-artwork #search-ui, body.is-artwork #s-backdrop, body.is-artwork #s-results { display: none !important; }
  body.is-artwork { height: auto; overflow: visible; }
  #artwork-view { display: none; min-height: 100dvh; flex-direction: column; }
  body.is-artwork #artwork-view { display: flex; }
  #aw-content { flex: 1; }
  :root { --muted: #737373; }
  .aw-muted { color: var(--muted); }

  /* header on an artwork page: the artwork number instead of the total, menu hidden, [ Close ] instead of About */
  .logo .md-only, .nav-menu { transition: opacity .5s; }
  body.is-artwork .logo .md-only, body.is-artwork #navbar .nav-menu { opacity: 0; pointer-events: none; }
  .aw-close { display: none !important; }
  body.is-artwork .aw-close { display: flex !important; }
  body.is-artwork .nav-about { display: none !important; }

  .aw-main { min-height: 100vh; display: grid; grid-template-columns: 1fr; gap: 4rem; padding-top: 8rem; padding-bottom: 2rem; color: var(--foreground); }
  @media (min-width: 1024px) { .aw-main { grid-template-columns: repeat(3, minmax(0, 1fr)); } }

  /* left: story + artist, pinned while the page scrolls */
  .aw-left { order: 3; display: flex; flex-direction: column; justify-content: space-between; gap: 4rem; padding-bottom: 2rem; }
  @media (min-width: 1024px) { .aw-left { order: 1; align-self: start; position: sticky; top: 8rem; height: calc(100dvh - 8rem); padding-bottom: 0; } }
  .aw-story-wrap { display: flex; width: 100%; flex-direction: column; gap: 1.5rem; }
  .aw-story { width: 100%; }
  .aw-story-btn { display: flex; width: 100%; align-items: flex-start; justify-content: space-between; gap: .5rem; text-align: left; cursor: pointer; }
  .aw-toggle { display: flex; flex-shrink: 0; align-items: center; justify-content: center; gap: 8.5px; text-transform: uppercase; user-select: none; }
  .aw-br { display: inline-block; transition: transform .3s; }
  .aw-story-btn:hover .aw-br-l { transform: translateX(.375rem); }
  .aw-story-btn:hover .aw-br-r { transform: translateX(-.375rem); }
  .aw-minus, .aw-plus { width: 1rem; height: 1rem; align-items: center; justify-content: center; }
  .aw-minus { display: none; }
  .aw-plus { display: flex; }
  .aw-story[data-state="open"] .aw-minus { display: flex; }
  .aw-story[data-state="open"] .aw-plus { display: none; }
  .aw-story-rows { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .8s cubic-bezier(.23, 1, .32, 1); }
  .aw-story[data-state="open"] .aw-story-rows { grid-template-rows: 1fr; }
  .aw-story-clip { min-height: 0; overflow: hidden; }
  .aw-story-text { padding-top: 1rem; line-height: 1.625; color: var(--muted); opacity: 0; transition: opacity .8s cubic-bezier(.23, 1, .32, 1); }
  .aw-story[data-state="open"] .aw-story-text { opacity: 1; }
  .aw-story-text p { margin-bottom: 1rem; }
  .aw-story-text p:last-child { margin-bottom: 0; }
  .aw-artist { display: flex; flex-direction: column; color: var(--foreground); }
  @media (min-width: 1024px) { .aw-artist { margin-bottom: 1.25rem; } }
  .aw-artist-name { vertical-align: bottom; margin-left: -.25rem; }
  .aw-artist-link { display: inline-flex; }
  .lbl.lbl-ink::after { background: #000; }
  .group:hover .lbl.lbl-ink { color: #fff; }
  .aw-note-mobile { color: var(--muted); }
  @media (min-width: 768px) { .aw-note-mobile { display: none; } }

  /* centre: the artwork, then details; a swipeable row on small screens */
  .aw-center { order: 2; }
  .aw-gallery {
    position: relative; left: 50%; display: flex; width: 100vw; max-width: 100vw; transform: translateX(-50%);
    flex-direction: row; gap: 1.5rem; overflow-x: auto; overscroll-behavior-x: contain; padding-bottom: 2.5rem;
    scroll-snap-type: x mandatory; scroll-padding: 0 1rem; scrollbar-width: none;
  }
  .aw-gallery::-webkit-scrollbar { display: none; }
  .aw-gallery::before, .aw-gallery::after { content: ""; width: 1rem; flex-shrink: 0; }
  .aw-slide { position: relative; display: flex; width: calc((100vw - 3rem) / 1.2); max-width: 100%; flex-shrink: 0; scroll-snap-align: center; align-items: flex-start; justify-content: center; }
  @media (min-width: 1024px) {
    .aw-gallery { position: static; width: 100%; max-width: 100%; transform: none; flex-direction: column; align-items: center; gap: 7.5rem; overflow-x: visible; padding-bottom: 0; scroll-snap-type: none; }
    .aw-gallery::before, .aw-gallery::after { display: none; }
    .aw-slide { width: 100%; flex-shrink: 1; scroll-snap-align: unset; }
  }
  /* image reveal: a white cover then a black panel slide away to the right while the picture glides in */
  .aw-media { position: relative; overflow: hidden; width: 100%; }
  .aw-move { transform: translateX(-20%) scale(1.2); transition: transform 1.2s cubic-bezier(.19, 1, .22, 1) .2s; }
  .aw-media.is-shown .aw-move { transform: translateX(0) scale(1); }
  .aw-move img { display: block; width: 100%; height: auto; }
  .aw-detail { position: relative; aspect-ratio: 4 / 5; overflow: hidden; }
  .aw-detail img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transform: scale(2.2); }
  .aw-panel { position: absolute; inset: 0; transform-origin: right; transition: transform 1s cubic-bezier(.8, 0, 0, 1); }
  .aw-panel-ink { background: var(--foreground); transition-delay: .1s; }
  .aw-panel-paper { background: rgba(255, 255, 255, .98); }
  .aw-media.is-shown .aw-panel { transform: scaleX(0); }

  /* right: the facts, then the source note further down */
  .aw-right { order: 1; display: flex; flex-direction: column; gap: 5rem; }
  @media (min-width: 768px) { .aw-right { gap: 30rem; } }
  @media (min-width: 1024px) { .aw-right { order: 3; } }
  .aw-sheet { letter-spacing: -0.04em; }
  @media (min-width: 1024px) { .aw-sheet { padding-top: 80%; } }
  .aw-sheet-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 1rem; row-gap: .75rem; }
  @media (min-width: 768px) { .aw-sheet-grid { column-gap: 2rem; } }
  .aw-sheet-grid .is-wide { grid-column: span 2; margin-bottom: .5rem; }
  .aw-sheet dt { color: var(--muted); }
  .aw-note-desk { display: none; color: var(--muted); }
  @media (min-width: 768px) { .aw-note-desk { display: block; } }

  /* source note: each line wiped in by a black bar */
  .reveal-lines { visibility: hidden; }
  .reveal-lines.is-inview { visibility: visible; }
  .reveal-lines .line-mask {
    --reveal-delay: calc(var(--line) * .15s); --reveal-duration: 1.8s;
    position: relative; display: block; width: fit-content; overflow: hidden;
    margin: -0.08em 0; padding: 0.08em 0;
  }
  .reveal-lines .line-mask::before, .reveal-lines .line-mask::after {
    content: ""; pointer-events: none; will-change: transform; position: absolute; inset: 0 -0.2em;
    transition: transform var(--reveal-duration) var(--ease-magic) var(--reveal-delay);
  }
  .reveal-lines .line-mask::before { background: var(--background); transform-origin: 0 center; z-index: 2; transform: scaleX(1); }
  .reveal-lines .line-mask::after { background: var(--foreground); z-index: 3; transform: translate(-140%); }
  .reveal-lines.is-inview .line-mask::before { transform-origin: 100% center; transform: scaleX(0); }
  .reveal-lines.is-inview .line-mask::after { transform: translate(140%); }

  /* the other works, in a strip at the end */
  .aw-related-inner { border-top: 1px solid #d4d4d4; padding-top: 4rem; margin: 0 1rem; }
  @media (min-width: 96rem) { .aw-related-inner { margin: 0 1.5rem; } }
  .aw-track-bleed { position: relative; left: 50%; width: 100vw; transform: translateX(-50%); overflow: visible; }
  .aw-track { overflow-x: auto; overflow-y: visible; padding-bottom: 1.5rem; scroll-padding-left: 1rem; scrollbar-width: thin; scrollbar-color: #171717 transparent; }
  @media (min-width: 1024px) { .aw-track { scroll-padding-left: 1.5rem; } }
  .aw-track::-webkit-scrollbar { height: 3px; }
  .aw-track::-webkit-scrollbar-thumb { background: #171717; }
  .aw-track-row { display: flex; min-width: max-content; gap: 1rem; }
  @media (min-width: 768px) { .aw-track-row { gap: 1.5rem; } }
  .aw-item { display: block; width: 58vw; flex-shrink: 0; transition: transform .3s; }
  .aw-item:hover { transform: scale(.98); }
  @media (min-width: 768px) { .aw-item { width: 12rem; } }
  .aw-item-cap { margin-bottom: .5rem; display: flex; align-items: center; gap: .75rem; font-size: .625rem; line-height: 130%; letter-spacing: -0.02em; }
  .aw-item.is-current .aw-item-cap { color: var(--muted); }
  .aw-item-num { flex-shrink: 0; }
  .aw-item-name { min-width: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; text-align: right; }
  .aw-item-check { opacity: .5; margin-left: .5rem; }
  .aw-item-check svg { display: inline; margin: 0 .125rem; width: .25em; height: .25em; transform: translateY(-1px); }
  .aw-item-img { position: relative; aspect-ratio: 1; width: 100%; overflow: hidden; background: rgba(23, 23, 23, .1); }
  .aw-item-img img { width: 100%; height: 100%; object-fit: cover; }

  /* ---------- presentation pages: news, article, services, contact, cookies ----------
     built from the same pieces as the About and artwork pages so they read as one site */
  body.is-page #content { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
  body.is-page #search-ui, body.is-page #s-backdrop, body.is-page #s-results { display: none !important; }
  body.is-page { height: auto; overflow: visible; }
  #page-view { display: none; min-height: 100dvh; flex-direction: column; overflow-x: clip; }
  body.is-page #page-view { display: flex; }
  .pg-main { flex: 1; padding-top: 126px; }
  .pg-main > * { animation: ab-fade-in 1s var(--ease) both; }
  .pg-main > * + * { animation-delay: .15s; }

  /* the header holds more links now: between tablet and small desktop they move into the Menu */
  .nav-end { gap: 1rem; white-space: nowrap; }
  @media (min-width: 768px) and (max-width: 1299px) {
    #navbar .nav-menu, #navbar .nav-end { display: none; }
    #menu-btn.mobile-only { display: block !important; grid-column: 3; }
  }
  body.is-artwork #navbar .nav-extra { display: none !important; }

  /* footer: cookie policy in the third column */
  .l-footer-end a { margin: -.25rem; display: inline-flex; }

  /* intro: the centred text column of the About page */
  .pg-col { width: 352px; max-width: 100%; margin: 2rem auto 5rem; }
  @media (min-width: 1024px) { .pg-col { margin: 129px auto 7rem; } }
  .pg-col .text-paint > * + * { margin-top: 1.75rem; }
  .pg-col h2 { font-size: .8125rem; font-weight: 400; text-transform: uppercase; }
  .pg-col .text-paint > h2:not(:first-child) { margin-top: 3rem; }
  .pg-col .text-paint > h2 + p { margin-top: .75rem; }
  .pg-status { margin-top: 3rem; }
  .pg-actions { margin-top: 1rem; margin-left: -.25rem; }

  /* list rows (news) reuse the artworks table; hovering a row shows its picture like the List page */
  .pg-list { padding-bottom: 4rem; }
  .pg-list .l-thumb { right: 12rem; top: 50%; bottom: auto; translate: 0 -50%; pointer-events: none; }
  @media (min-width: 768px) { .pg-list .l-thumb { display: block; } }
  .pg-list .ab-item:hover .l-thumb { scale: 1; opacity: 1; }

  /* news article: picture | text | facts, like an artwork page */
  .pg-art { display: grid; grid-template-areas: "text" "fig" "sheet"; gap: 3rem; padding: 2rem 0 6rem; letter-spacing: -0.04em; }
  @media (min-width: 1024px) {
    .pg-art { grid-template-areas: "fig text sheet"; grid-template-columns: minmax(0, 1fr) 352px minmax(0, 1fr); column-gap: 2rem; padding-top: 129px; }
    .pg-art-fig, .pg-art-sheet { position: sticky; top: 126px; align-self: start; }
  }
  .pg-art-text { grid-area: text; }
  .pg-art-fig { grid-area: fig; margin: 0; }
  .pg-art-fig .reveal-image { aspect-ratio: 4 / 5; width: min(100%, 343px); }
  .pg-art-fig figcaption { margin-top: .5rem; }
  .pg-art-sheet { grid-area: sheet; }
  .pg-main .aw-sheet { padding-top: 0; }
  .pg-back { margin: 0 0 2.5rem -.25rem; }
  .pg-date { color: var(--muted); margin-bottom: .5rem; }
  .pg-title { margin-bottom: 1.75rem; font-size: .8125rem; font-weight: 400; text-transform: uppercase; }
  .pg-lead { margin-bottom: 1.75rem; }
  .pg-body > p + p { margin-top: 1.75rem; }
  .pg-ink { margin-left: -.25rem; }
  .pg-ink a { display: inline-flex; }

  /* services: four sheets with a picture each, like the strip under an artwork */
  .pg-services { display: grid; grid-template-columns: 1fr; gap: 4rem 1rem; padding-bottom: 6rem; letter-spacing: -0.04em; }
  @media (min-width: 768px) { .pg-services { grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 2rem; } }
  @media (min-width: 1024px) { .pg-services { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
  .pg-svc .reveal-image { aspect-ratio: 4 / 5; margin-bottom: 1rem; }
  .pg-svc-title { margin: .25rem 0 1rem; font-size: .8125rem; font-weight: 400; text-transform: uppercase; }
  .pg-svc p { margin-bottom: 1rem; }
  .pg-svc ul { list-style: none; margin-bottom: 1.25rem; color: var(--muted); }
  .pg-svc li::before { content: "— "; }
  .pg-svc .lbl-row { margin-left: -.25rem; width: fit-content; }
  .pg-member { padding-top: .75rem; border-top: 1px solid rgba(0, 0, 0, .3); }
  .pg-member .pg-svc-title { margin-bottom: .25rem; }
  .pg-member p + p { margin-top: 1rem; }
  .pg-member .pg-ink { display: inline-block; margin-top: 1.25rem; }

  /* contact: facts on the left like an artwork sheet, the form across the other two columns */
  .pg-contact { display: grid; grid-template-columns: 1fr; gap: 4rem; padding-bottom: 6rem; letter-spacing: -0.04em; }
  @media (min-width: 1024px) { .pg-contact { grid-template-columns: repeat(3, minmax(0, 1fr)); column-gap: 2rem; } }
  .pg-contact .aw-sheet-grid { align-content: start; }
  .pg-form, .pg-sent { display: grid; gap: 1.5rem; align-content: start; }
  @media (min-width: 1024px) { .pg-form, .pg-sent { grid-column: span 2; } }
  .pg-form-row { display: grid; gap: 1.5rem; }
  @media (min-width: 768px) { .pg-form-row { grid-template-columns: 1fr 1fr; column-gap: 2rem; } }
  .pg-field { position: relative; display: grid; gap: .35rem; }
  .pg-label { color: var(--muted); }
  .pg-field input, .pg-field textarea {
    width: 100%; padding: .35rem 0; border: 0; border-bottom: 1px solid rgba(0, 0, 0, .3); border-radius: 0;
    background: transparent; font: inherit; letter-spacing: inherit; color: var(--foreground); outline: none; appearance: none;
    transition: border-color .2s;
  }
  .pg-field textarea { min-height: 7.5rem; resize: vertical; }
  .pg-field input:focus, .pg-field textarea:focus { border-bottom-color: var(--foreground); }
  .pg-field.has-error input, .pg-field.has-error textarea { border-bottom-color: var(--foreground); }
  /* subject picker: [ + ] toggle like the Story panel, options with a [■] check like the List filters */
  .pg-sel-btn {
    display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 1rem;
    padding: .35rem 0; border-bottom: 1px solid rgba(0, 0, 0, .3); text-align: left; letter-spacing: inherit;
    transition: border-color .2s;
  }
  .pg-select[data-open="true"] .pg-sel-btn, .pg-sel-btn:focus-visible { border-bottom-color: var(--foreground); outline: none; }
  .pg-sel-btn:hover .aw-br-l, .pg-select[data-open="true"] .aw-br-l { transform: translateX(.375rem); }
  .pg-sel-btn:hover .aw-br-r, .pg-select[data-open="true"] .aw-br-r { transform: translateX(-.375rem); }
  .pg-sel-minus, .pg-sel-plus { display: flex; width: 1rem; height: 1rem; align-items: center; justify-content: center; }
  .pg-sel-minus, .pg-select[data-open="true"] .pg-sel-plus { display: none; }
  .pg-select[data-open="true"] .pg-sel-minus { display: flex; }
  .pg-sel-list { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .5s var(--ease-in-out); }
  .pg-select[data-open="true"] .pg-sel-list { grid-template-rows: 1fr; }
  .pg-sel-clip { min-height: 0; overflow: hidden; }
  .pg-sel-opts { display: flex; flex-direction: column; align-items: flex-start; gap: .375rem; padding: .75rem 0 .25rem; }
  .pg-opt { display: inline-flex; overflow: hidden; text-align: left; letter-spacing: inherit; outline: none; }
  .pg-opt-inner {
    display: inline-flex; align-items: center; gap: .375rem; transform: translateY(110%);
    transition: transform .5s var(--ease-in-out); transition-delay: 0s;
  }
  .pg-select[data-open="true"] .pg-opt-inner { transform: translateY(0); transition-delay: calc(var(--i) * 45ms); }
  .pg-opt[aria-selected="true"] .check svg { transform: translateY(-1px) scale(1); }
  .pg-opt:focus-visible .lbl::after { transform: scaleX(1); }
  .pg-opt:focus-visible .lbl { color: #fff; }
  .pg-error { min-height: 1.3em; color: var(--foreground); text-transform: uppercase; }
  .pg-error:empty { min-height: 0; }
  .pg-error:not(:empty)::before { content: "[ ! ] "; }
  .pg-check { display: flex; gap: .4rem; align-items: baseline; cursor: pointer; color: var(--muted); }
  .pg-check input { position: absolute; opacity: 0; pointer-events: none; }
  .pg-check .box { color: var(--foreground); flex-shrink: 0; }
  .pg-check .box svg { display: inline; margin: 0 .125rem; width: .25em; height: .25em; transform: translateY(-1px) scale(0); transition: transform .2s; }
  .pg-check input:checked + .box svg { transform: translateY(-1px) scale(1); }
  .pg-check input:focus-visible + .box { outline: 1px solid currentColor; outline-offset: 2px; }
  .pg-hp { position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden; }
  .pg-submit { justify-self: start; margin-left: -.25rem; text-transform: uppercase; }
  .pg-submit[disabled] { opacity: .5; pointer-events: none; }
  .pg-sent .lbl-row { margin-left: -.25rem; width: fit-content; }

  /* cookie banner: a black card like the hover fills, with bracket buttons */
  #cookie-banner {
    position: fixed; left: 1rem; bottom: 1rem; z-index: 9990; width: 22rem; max-width: calc(100vw - 2rem);
    padding: 1rem; background: #000; color: #fff; visibility: hidden; opacity: 0;
  }
  @media (min-width: 96rem) { #cookie-banner { left: 1.5rem; bottom: 1.5rem; } }
  #cookie-banner p { margin-bottom: 1rem; color: #d4d4d4; }
  .cb-actions { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem 1rem; margin-left: -.25rem; }


  /* ---------- gallery view (/gallery) ---------- */
  #gallery-view {
    position: fixed; inset: 0; display: none; height: 100dvh; overflow: hidden;
    cursor: grab; touch-action: none; user-select: none; -webkit-user-select: none; visibility: hidden;
  }
  body.is-gallery #gallery-view { display: block; }
  #gallery-view[data-dragging="true"], #gallery-view[data-dragging="true"] .g-link { cursor: grabbing; }
  .g-canvas { position: relative; width: 100%; height: 100%; overflow: hidden; }
  .g-canvas canvas { display: block; }
  .g-link { position: absolute; inset: 0; -webkit-user-drag: none; }
  .g-label {
    position: fixed; top: 0; left: 0; z-index: 50; display: none; pointer-events: none;
    color: var(--background); mix-blend-mode: difference; opacity: 0;
    font-size: .625rem; line-height: 130%; letter-spacing: -0.02em; text-transform: uppercase;
  }
  @media (pointer: fine) { .g-label { display: block; } }
  .g-label .g-name { white-space: nowrap; }
  #list-view { display: none; min-height: 100dvh; flex-direction: column; }
  body.is-list #list-view { display: flex; }
  .list-main { flex: 1; padding-top: 5rem; padding-bottom: 2.5rem; overflow: hidden; }

  /* list page footer */
  .l-footer { margin-top: 2rem; flex-shrink: 0; padding-bottom: 1.25rem; }
  .l-footer-rule { border-top: 1px solid #d4d4d4; padding-top: 1.25rem; }
  .l-footer-title { margin-bottom: 2.5rem; text-transform: uppercase; }
  .l-footer-grid { display: grid; grid-template-columns: 1fr; gap: .5rem; }
  .l-footer-muted { color: rgba(0, 0, 0, .5); text-transform: uppercase; }
  @media (min-width: 768px) {
    .l-footer-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }


  .list-bar { transition: opacity 1s; }
  .lb-row { display: flex; min-width: 0; align-items: center; gap: 1.25rem; }
  .lb-left { display: flex; min-width: 0; flex: 1; align-items: center; gap: 1.25rem; }
  .lb-btn { flex-shrink: 0; text-transform: uppercase; user-select: none; }
  .lb-btn[data-active="true"] { color: #000; }
  .lb-search {
    margin-left: auto; display: flex; max-width: calc(100vw - 10rem); min-width: 0; flex-shrink: 1;
    align-items: center; gap: .75rem; border-bottom: 1px solid transparent;
  }
  @media (min-width: 768px) { .lb-search { max-width: none; flex-shrink: 0; } }
  .lb-search.is-open { border-bottom-color: color-mix(in srgb, currentColor 35%, transparent); }
  .lb-input-wrap { display: flex; min-width: 0; align-items: center; gap: .5rem; overflow: hidden; width: 0; max-width: 0; opacity: 0; }
  .lb-search.is-open .lb-input-wrap { width: 100%; max-width: 100%; opacity: 1; }
  @media (min-width: 768px) { .lb-search.is-open .lb-input-wrap { width: 18rem; } }
  .lb-input-wrap input { height: 1.5rem; min-width: 0; flex: 1; appearance: none; outline: none; }
  .lb-input-wrap input::-webkit-search-cancel-button { display: none; appearance: none; }

  .list-body { margin-top: 1.5rem; }
  @media (min-width: 768px) { .list-body { margin-top: .75rem; } }
  .l-cats { position: absolute; z-index: 0; height: 0; overflow: hidden; pointer-events: none; transition: height .3s var(--ease-in); }
  @media (min-width: 768px) { .l-cats { height: auto; } }
  .l-cats.is-open { z-index: 10; height: 8rem; overflow: visible; pointer-events: auto; }
  .l-cats-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: .75rem; row-gap: .75rem; }
  @media (min-width: 768px) { .l-cats-grid { column-gap: 1.5rem; } }
  .l-cat { display: inline-flex; width: 100%; align-items: center; gap: .375rem; overflow: hidden; text-align: left; line-height: 1; white-space: nowrap; }
  .l-cat-inner { display: inline-flex; align-items: center; gap: .375rem; pointer-events: none; transform: translateY(100%); transition: transform .5s var(--ease-in-out); }
  .l-cats.is-open .l-cat-inner { pointer-events: auto; transform: translateY(0); }
  .l-cat .check svg { display: inline; margin: 0 .125rem; width: .25em; height: .25em; transform: translateY(-1px) scale(0); transition: transform .2s; }
  .l-cat.is-checked .check svg { transform: translateY(-1px) scale(1); }

  .l-list { width: 100%; letter-spacing: -0.04em; transition: all 1s var(--ease-in-out); }
  .filters-open .l-list { transform: translateY(7rem); }
  @media (min-width: 768px) {
    .l-list { letter-spacing: normal; margin-left: auto; transform: translateY(-.75rem); }
    .filters-open .l-list { width: 50%; transform: translateY(-.75rem); }
  }
  @media (min-width: 1024px) { .filters-open .l-list { width: 63%; } }
  .l-row { position: relative; display: block; width: 100%; padding: .875rem 1rem; text-align: left; letter-spacing: -0.03em; }
  @media (min-width: 96rem) { .l-row { padding-left: 1.5rem; padding-right: 1.5rem; } }
  .l-bar { position: absolute; inset: 0; display: none; background: #171717; opacity: 0; pointer-events: none; }
  .l-text { position: relative; top: 50%; z-index: 10; display: flex; height: auto; align-items: center; transition: all 1s var(--ease-in-out); }
  .l-divider { position: absolute; top: 0; left: 1rem; right: 1rem; height: 1px; background: rgba(23, 23, 23, .1); pointer-events: none; }
  .l-thumb {
    position: absolute; right: 5rem; bottom: -50%; z-index: 10; display: none;
    width: 6rem; height: 6rem; object-fit: cover; opacity: 0; scale: .9;
    transition: opacity .2s cubic-bezier(.4,0,.2,1), scale .2s cubic-bezier(.4,0,.2,1);
  }
  .l-row:hover .l-thumb { scale: 1; opacity: 1; }
  @media (min-width: 768px) {
    .l-row { display: flex; align-items: center; justify-content: flex-start; height: 1.5rem; padding-top: .25rem; padding-bottom: 0; }
    .l-bar, .l-thumb { display: block; }
    .l-row:hover .l-bar { opacity: 1; }
    .l-row:hover .reveal-block { color: var(--background); }
    .l-text { position: absolute; left: 50vw; transform: translate(-50%, -50%); }
    .filters-open .l-text { left: 0; transform: translate(0, -50%); padding-left: .5rem; }
    .l-divider { display: none; }
  }
  .l-empty { margin-top: 1.25rem; font-size: .875rem; line-height: 130%; letter-spacing: -0.05em; transition: transform .6s var(--ease-in-out) .5s; }
  .filters-open .l-empty { transform: translateY(8rem); transition-delay: 0s; }
  @media (min-width: 768px) { .filters-open .l-empty { transform: translateY(7rem); } }
  .l-empty p { color: rgba(23, 23, 23, .5); }
  .l-empty em { font-weight: 700; font-style: italic; }

  /* black-bar text reveal: white cover shrinks away while a black bar sweeps across */
  .reveal-block {
    --reveal-delay: 0ms; --reveal-duration: 1.1s;
    position: relative; display: inline-block; overflow: hidden; visibility: hidden;
  }
  .reveal-block.is-inview { visibility: visible; }
  .reveal-block::before, .reveal-block::after {
    content: ""; pointer-events: none; will-change: transform; position: absolute; inset: 0 -0.2em;
    transition-property: transform; transition-duration: var(--reveal-duration);
    transition-timing-function: var(--ease-magic); transition-delay: var(--reveal-delay);
  }
  .reveal-block::before { background: var(--background); transform-origin: 0 center; z-index: 2; transform: scaleX(1); }
  .reveal-block::after { background: var(--foreground); z-index: 3; transform: translate(-140%); }
  .reveal-block.is-inview::before { transform-origin: 100% center; transform: scaleX(0); }
  .reveal-block.is-inview::after { transform: translate(140%); }
  @media (prefers-reduced-motion: reduce) {
    .reveal-block::before, .reveal-block::after { transition: none; }
    .reveal-block::before { transform: scaleX(0); }
    .reveal-block::after { transform: translate(140%); }
  }

  #mobile-menu {
    position: fixed; inset: 0; z-index: 9998; display: none; flex-direction: column; justify-content: space-between;
    background: #000; color: #fff; padding-top: 2rem;
    clip-path: polygon(-1% 0%, -1% 0%, -1% 100%, -1% 100%);
  }
  #mobile-menu nav { margin-top: 8rem; display: flex; flex-direction: column; align-items: flex-start; gap: 2.25rem; }
  #mobile-menu .m-foot { border-top: 1px solid #fff; padding-top: 1.25rem; padding-bottom: 1rem; }
  #mobile-menu .m-foot-row { display: flex; align-items: center; justify-content: space-between; }
</style>
<?php wp_head(); ?>
</head>
<body>

<header id="navbar" class="px-global text-sm uppercase">
  <div class="nav-grid">
    <a class="logo" href="#/">
      <span class="text-sm tabular">48</span> <span class="md-only text-sm" data-i18n="titleSuffix">Art Works</span>
    </a>
    <nav class="nav-menu">
      <a class="group lbl-row text-sm uppercase" href="#/">
        <span class="lbl-bracket">[</span><span class="lbl is-current"><span data-i18n="grid">Grid</span></span><span class="lbl-bracket">]</span>
      </a>
      <a class="group lbl-row text-sm uppercase" href="#/archive">
        <span class="lbl-bracket">[</span><span class="lbl"><span data-i18n="list">List</span></span><span class="lbl-bracket">]</span>
      </a>
      <a class="group lbl-row text-sm uppercase" href="#/gallery">
        <span class="lbl-bracket">[</span><span class="lbl"><span data-i18n="gallery">Gallery</span></span><span class="lbl-bracket">]</span>
      </a>
    </nav>
    <div class="nav-end">
      <a class="group lbl-row text-sm uppercase nav-about" href="#/about">
        <span class="lbl"><span data-i18n="aboutAction">About the Project</span></span>
      </a>
      <a class="group lbl-row text-sm uppercase nav-extra" href="#/team">
        <span class="lbl"><span data-i18n="team">Team</span></span>
      </a>
      <a class="group lbl-row text-sm uppercase nav-extra" href="#/news">
        <span class="lbl"><span data-i18n="news">News</span></span>
      </a>
      <a class="group lbl-row text-sm uppercase nav-extra" href="#/services">
        <span class="lbl"><span data-i18n="services">Services</span></span>
      </a>
      <a class="group lbl-row text-sm uppercase nav-extra" href="#/contact">
        <span class="lbl"><span data-i18n="contact">Contact</span></span>
      </a>
      <a class="group lbl-row text-sm uppercase aw-close" href="#/close">
        <span class="lbl-bracket">[</span><span class="lbl"><span data-i18n="close">Close</span></span><span class="lbl-bracket">]</span>
      </a>
    </div>
    <button type="button" class="menu-btn mobile-only uppercase" id="menu-btn" data-i18n="menu">Menu</button>
  </div>
</header>

<div id="search-ui" class="px-global">
  <search class="search-col text-sm uppercase">
    <div class="search-row">
      <button type="button" class="uppercase" id="filters-btn" aria-expanded="false" style="flex-shrink:0"></button>
      <div class="search-field" id="search-field">
        <div class="search-input-wrap">
          <label for="search-input" class="sr-only" data-i18n="searchArtwork">Search artwork</label>
          <input id="search-input" type="search" autocomplete="off">
          <button type="button" class="clear-btn" id="clear-input" hidden data-i18n="clearInput">Clear</button>
        </div>
        <button type="button" class="uppercase" id="close-search" hidden style="flex-shrink:0">⨯</button>
        <button type="button" class="group uppercase" id="open-search" style="flex-shrink:0">
          <span class="lbl"><span data-i18n="search">Search</span></span>
        </button>
      </div>
    </div>
    <div class="cats" id="cats" hidden><div class="cats-inner" id="cats-inner"></div></div>
  </search>
</div>

<div id="s-backdrop" hidden aria-hidden="true"></div>
<div id="s-results" class="px-global" role="region" hidden><div id="s-results-inner"></div></div>

<div id="content" class="is-invisible no-pointer">
  <div id="draggable"></div>
</div>

<div id="list-view">
 <main class="list-main" id="list-main">
  <section class="list-bar px-global text-sm uppercase">
    <div class="lb-row">
      <div class="lb-left">
        <button type="button" class="lb-btn" id="l-filters" aria-expanded="false"></button>
      </div>
      <div class="lb-search" id="l-search">
        <div class="lb-input-wrap">
          <label for="l-search-input" class="sr-only" data-i18n="searchArtwork">Search artwork</label>
          <input id="l-search-input" type="search" autocomplete="off">
          <button type="button" class="clear-btn" id="l-clear" hidden data-i18n="clearInput">Clear</button>
        </div>
        <button type="button" class="lb-btn" id="l-close" hidden>⨯</button>
        <button type="button" class="lb-btn" id="l-open" data-i18n="search">Search</button>
      </div>
    </div>
  </section>
  <p class="sr-only" aria-live="polite" aria-atomic="true" id="l-count"></p>
  <div class="list-body" id="list-body">
    <aside class="l-cats px-global text-sm" id="l-cats"><div class="l-cats-grid" id="l-cats-grid"></div></aside>
    <div class="l-empty px-global" id="l-empty" hidden></div>
    <div class="l-list text-sm" id="l-list"></div>
  </div>
 </main>
 <footer class="l-footer px-global text-sm" id="l-footer">
  <div class="l-footer-rule"></div>
  <div class="l-footer-title"><span data-count>48</span> <span data-i18n="titleSuffix">Art Works</span></div>
  <div class="l-footer-grid">
    <p class="l-footer-muted" data-i18n="rights">All rights reserved ©</p>
    <p class="l-footer-muted" data-i18n="place">Skopje, North Macedonia</p>
    <p class="l-footer-muted l-footer-end"><a class="group" href="#/cookies"><span class="lbl lbl-ink"><span data-i18n="cookies">Cookie policy</span></span></a></p>
  </div>
 </footer>
</div>

<div id="about-view">
 <main class="about-main px-global" id="about-main">
  <section class="ab-desk" id="ab-desk">
    <div class="ab-fig" style="top:0;right:6rem;width:222px;height:313px" data-speed="-0.2"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/artist.jpg" alt="Vangel Naumovski" width="222" height="313"></div></div>
    <div class="ab-fig" style="top:423px;left:0;width:221px;height:166px" data-speed="0.2"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/15.jpg" alt="" width="221" height="166"></div></div>
    <div class="ab-fig" style="top:1045px;left:0;width:343px;height:397px" data-speed="-0.3"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/07.jpg" alt="" width="343" height="397"></div></div>
    <div class="ab-fig" style="top:852px;right:0;width:221px;height:147px" data-speed="-0.1"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/27.jpg" alt="" width="221" height="147"></div></div>
    <div class="ab-fig" style="top:1547px;right:6px;width:339px;height:226px" data-speed="-0.1"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/38.jpg" alt="" width="339" height="226"></div></div>
    <div class="ab-fig" style="top:1815px;left:111px;width:221px;height:298px" data-speed="-0.1"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/33.jpg" alt="" width="221" height="298" style="object-position:38% center"></div></div>
    <div class="ab-fig" style="top:2134px;right:0;width:459px;height:345px" data-speed="-0.1"><div class="reveal-image"><img src="<?php echo esc_url( $vn_base ); ?>images/10.jpg" alt="" width="459" height="345"></div></div>
    <article class="ab-article text-sm" id="ab-article">
      <h1 class="ab-title" data-i18n="aboutTitle">About the artist</h1>
      <div class="text-paint ab-paras" id="ab-paras"></div>
    </article>
  </section>
  <section class="ab-mobile" id="ab-mobile"></section>
  <section class="ab-works" id="ab-works">
    <div class="ab-works-head">
      <p class="uppercase" data-i18n="works">Artworks</p>
      <div class="ab-works-search">
        <div class="lb-search" id="a-search">
          <div class="lb-input-wrap">
            <label for="a-search-input" class="sr-only" data-i18n="searchArtwork">Search artwork</label>
            <input id="a-search-input" type="search" autocomplete="off">
            <button type="button" class="clear-btn" id="a-clear" hidden data-i18n="clearInput">Clear</button>
          </div>
          <button type="button" class="lb-btn" id="a-close" hidden>⨯</button>
          <button type="button" class="lb-btn" id="a-open" data-i18n="search">Search</button>
        </div>
      </div>
    </div>
    <p class="sr-only" aria-live="polite" aria-atomic="true" id="a-count"></p>
    <div class="ab-rule"></div>
    <div id="a-empty" class="ab-empty" hidden></div>
    <ul class="ab-list" id="a-list"></ul>
  </section>
 </main>
 <footer class="l-footer px-global text-sm" id="a-footer">
  <div class="l-footer-rule"></div>
  <div class="l-footer-title"><span data-count>48</span> <span data-i18n="titleSuffix">Art Works</span></div>
  <div class="l-footer-grid">
    <p class="l-footer-muted" data-i18n="rights">All rights reserved ©</p>
    <p class="l-footer-muted" data-i18n="place">Skopje, North Macedonia</p>
    <p class="l-footer-muted l-footer-end"><a class="group" href="#/cookies"><span class="lbl lbl-ink"><span data-i18n="cookies">Cookie policy</span></span></a></p>
  </div>
 </footer>
</div>

<div id="artwork-view">
 <div id="aw-content">
  <main class="aw-main px-global" id="aw-main">
    <div class="aw-left">
      <div class="aw-story-wrap">
        <div class="aw-story" id="aw-story" data-state="closed">
          <button type="button" class="aw-story-btn text-sm uppercase" id="aw-story-btn">
            <span data-i18n="story">Story</span>
            <span class="aw-toggle"><span class="aw-br aw-br-l">[ </span> <span class="aw-minus">-</span><span class="aw-plus">+</span> <span class="aw-br aw-br-r"> ]</span></span>
          </button>
          <div class="aw-story-rows"><div class="aw-story-clip"><div class="aw-story-text text-sm" id="aw-story-text"></div></div></div>
        </div>
      </div>
      <div class="aw-note aw-note-mobile text-sm" id="aw-note-m"></div>
      <div class="aw-artist text-sm">
        <div class="aw-muted" data-i18n="artistLabel">Artist</div>
        <div><span class="reveal-block aw-artist-name" id="aw-artist-reveal"><a href="#/about" class="group aw-artist-link"><span class="lbl lbl-ink"><span>Vangel Naumovski</span></span></a></span></div>
      </div>
    </div>
    <div class="aw-center"><div class="aw-gallery" id="aw-gallery"></div></div>
    <div class="aw-right">
      <dl class="aw-sheet text-sm" id="aw-sheet"></dl>
      <div class="aw-note aw-note-desk text-sm" id="aw-note"></div>
    </div>
  </main>
 </div>
 <section class="aw-related" id="aw-related">
   <div class="aw-related-inner">
     <div class="aw-track-bleed"><div class="aw-track px-global" id="aw-track"><div class="aw-track-row" id="aw-track-row"></div></div></div>
   </div>
 </section>
 <footer class="l-footer px-global text-sm" id="w-footer">
  <div class="l-footer-rule"></div>
  <div class="l-footer-title"><span data-count>48</span> <span data-i18n="titleSuffix">Art Works</span></div>
  <div class="l-footer-grid">
    <p class="l-footer-muted" data-i18n="rights">All rights reserved ©</p>
    <p class="l-footer-muted" data-i18n="place">Skopje, North Macedonia</p>
    <p class="l-footer-muted l-footer-end"><a class="group" href="#/cookies"><span class="lbl lbl-ink"><span data-i18n="cookies">Cookie policy</span></span></a></p>
  </div>
 </footer>
</div>

<div id="page-view">
 <main class="pg-main px-global" id="pg-main"></main>
 <footer class="l-footer px-global text-sm" id="p-footer">
  <div class="l-footer-rule"></div>
  <div class="l-footer-title"><span data-count>48</span> <span data-i18n="titleSuffix">Art Works</span></div>
  <div class="l-footer-grid">
    <p class="l-footer-muted" data-i18n="rights">All rights reserved ©</p>
    <p class="l-footer-muted" data-i18n="place">Skopje, North Macedonia</p>
    <p class="l-footer-muted l-footer-end"><a class="group" href="#/cookies"><span class="lbl lbl-ink"><span data-i18n="cookies">Cookie policy</span></span></a></p>
  </div>
 </footer>
</div>

<main id="gallery-view">
  <div class="g-canvas" id="g-canvas"></div>
  <a class="g-link" id="g-link" href="#/artwork/1" draggable="false" aria-label=""></a>
  <div class="g-label" id="g-label"><p class="g-num"></p><p class="g-name"></p></div>
</main>

<div id="cookie-banner" class="text-sm" role="region" aria-label="Cookies">
  <p data-i18n="cookieText">We use one cookie to remember your choice and, if you accept, your language. Nothing else.</p>
  <div class="cb-actions uppercase">
    <button type="button" class="group lbl-row uppercase" id="cb-accept"><span class="lbl-bracket">[</span><span class="lbl"><span data-i18n="accept">Accept</span></span><span class="lbl-bracket">]</span></button>
    <button type="button" class="group lbl-row uppercase" id="cb-decline"><span class="lbl-bracket">[</span><span class="lbl"><span data-i18n="decline">Decline</span></span><span class="lbl-bracket">]</span></button>
    <a class="group lbl-row uppercase" href="#/cookies"><span class="lbl"><span data-i18n="cookiesMore">Cookie policy</span></span></a>
  </div>
</div>

<div class="lang text-sm uppercase">
  <a class="group" href="#" data-locale="mk"><span class="lbl"><span>MK</span></span></a>
  <span aria-hidden="true">/</span>
  <a class="group" href="#" data-locale="en"><span class="lbl is-current"><span>EN</span></span></a>
</div>

<div id="mobile-menu" class="px-global text-sm">
  <div>
    <nav>
      <a class="uppercase" href="#/" data-i18n="grid">Grid</a>
      <a class="uppercase" href="#/archive" data-i18n="list">List</a>
      <a class="uppercase" href="#/gallery" data-i18n="gallery">Gallery</a>
      <a class="uppercase" href="#/about" data-i18n="about">About us</a>
      <a class="uppercase" href="#/team" data-i18n="team">Team</a>
      <a class="uppercase" href="#/news" data-i18n="news">News</a>
      <a class="uppercase" href="#/services" data-i18n="services">Services</a>
      <a class="uppercase" href="#/contact" data-i18n="contact">Contact</a>
    </nav>
  </div>
  <div class="m-foot">
    <p class="uppercase" style="margin-bottom:5.5rem"><span data-count>48</span> <span data-i18n="titleSuffix">Art Works</span></p>
    <div class="m-foot-row uppercase"><span>© 2026</span></div>
  </div>
</div>

<script>
  // testing aid: ?timers drives animation frames with timers (for headless / hidden previews)
  if (location.search.includes("timers")) {
    window.requestAnimationFrame = (cb) => setTimeout(() => cb(performance.now()), 16);
    window.cancelAnimationFrame = (id) => clearTimeout(id);
  }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fuse.js/7.0.0/fuse.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lenis@1.3.17/dist/lenis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
(() => {
  const ASSET_BASE = <?php echo wp_json_encode( $vn_base ); ?>;
  window.VN_CONTACT = <?php echo wp_json_encode( array( 'url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'vn_contact' ) ) ); ?>;
  /* ============ config (values taken from the original layout) ============ */
  const COUNT = 197;
  const PER_ROW = 20;
  const FILL_IDS = [176, 177, 178];
  const BREAKPOINT = 768;
  const ITEM_REM = { mobile: 5.4375, desktop: 7.875 };
  const LAYOUT_REM = {
    mobile:  { title: 1.375, gapX: 4,   gapY: 4, rowOffset: 2.45, shiftY: 1.575, top: 5.5,  side: 1 },
    desktop: { title: 1.875, gapX: 7.5, gapY: 3, rowOffset: 5.6,  shiftY: 4,     top: 7.25, side: 2 },
  };
  const DIAMOND = [1, 3, 5, 3, 1];

  const DICT = {
    en: {
      titleSuffix: "Art Works", grid: "Grid", list: "List", gallery: "Gallery",
      about: "About the artist", aboutAction: "About the Artist", menu: "Menu", close: "Close",
      filters: "Filters", search: "Search", searchArtwork: "Search artwork", clearInput: "Clear",
      knowMore: "Learn more", backToGrid: "Back to grid", searchEmpty: "No results found.",
      noResults: "No artworks found", noResultsFor: "for", clear: "Clear search",
      countOne: "artwork found", countMany: "artworks found",
      rights: "All rights reserved ©", place: "Skopje, North Macedonia",
      aboutTitle: "About the artist", works: "Artworks", searchWorkPh: "Search artwork…", discover: "[ DISCOVER ]",
      story: "Story", artistLabel: "Artist", fTitle: "Title", fYear: "Year", fPeriod: "Period", fMedium: "Medium", fSize: "Size", fSource: "Source",
      team: "Team", news: "News", services: "Services", contact: "Contact", cookies: "Cookie policy",
      articles: "Articles", read: "[ READ ]", fDate: "Date", fSection: "Section", relatedWork: "Related artwork", nextArticle: "Next article",
      enquire: "Enquire", fEmail: "Email", fPhone: "Phone", fHours: "Hours", hoursVal: "Mon–Fri, 10:00–16:00", fAddress: "Address",
      fName: "Name", fSubject: "Subject", subjGeneral: "General question", fMessage: "Message",
      consent: "I agree that my message is stored so that you can reply to me.", send: "Send", sending: "Sending…",
      sentTitle: "Message sent", sentText: "Thank you for writing. We usually reply within a few working days.", sendAnother: "New message",
      errRequired: "Required", errEmail: "Enter a valid email address", errShort: "Please write a little more", errConsent: "Please agree to continue",
      errSend: "The message could not be sent. Please try again, or write to us by email.",
      cookieText: "We use one cookie to remember your choice and, if you accept, your language. Nothing else.",
      accept: "Accept", decline: "Decline", cookiesMore: "Cookie policy", cookieStatus: "Your choice",
      cAccepted: "accepted", cDeclined: "declined", cNone: "not chosen yet",
    },
    mk: {
      titleSuffix: "уметнички дела", grid: "Мрежа", list: "Листа", gallery: "Галерија",
      about: "За уметникот", aboutAction: "За уметникот", menu: "Мени", close: "Затвори",
      filters: "Филтри", search: "Пребарај", searchArtwork: "Пребарај дело", clearInput: "Избриши",
      knowMore: "Дознај повеќе", backToGrid: "Назад кон мрежата", searchEmpty: "Нема пронајдени резултати.",
      noResults: "Не се пронајдени дела", noResultsFor: "за", clear: "Исчисти пребарување",
      countOne: "пронајдено дело", countMany: "пронајдени дела",
      rights: "Сите права се задржани ©", place: "Скопје, Северна Македонија",
      aboutTitle: "За уметникот", works: "Дела", searchWorkPh: "Пребарај дело…", discover: "[ ОТКРИЈ ]",
      story: "Приказна", artistLabel: "Уметник", fTitle: "Наслов", fYear: "Година", fPeriod: "Период", fMedium: "Техника", fSize: "Димензии", fSource: "Извор",
      team: "Тим", news: "Вести", services: "Услуги", contact: "Контакт", cookies: "Политика за колачиња",
      articles: "Написи", read: "[ ПРОЧИТАЈ ]", fDate: "Датум", fSection: "Рубрика", relatedWork: "Поврзано дело", nextArticle: "Следен напис",
      enquire: "Побарај", fEmail: "Е-пошта", fPhone: "Телефон", fHours: "Работно време", hoursVal: "Пон–Пет, 10:00–16:00", fAddress: "Адреса",
      fName: "Име", fSubject: "Тема", subjGeneral: "Општо прашање", fMessage: "Порака",
      consent: "Се согласувам мојата порака да биде зачувана за да може да ми одговорите.", send: "Испрати", sending: "Се испраќа…",
      sentTitle: "Пораката е испратена", sentText: "Ви благодариме што ни пишавте. Обично одговараме во рок од неколку работни дена.", sendAnother: "Нова порака",
      errRequired: "Задолжително", errEmail: "Внесете валидна е-пошта", errShort: "Ве молиме напишете малку повеќе", errConsent: "Ве молиме потврдете за да продолжите",
      errSend: "Пораката не можеше да се испрати. Обидете се повторно или пишете ни по е-пошта.",
      cookieText: "Користиме едно колаче за да го запомниме вашиот избор и, ако прифатите, вашиот јазик. Ништо друго.",
      accept: "Прифати", decline: "Одбиј", cookiesMore: "Политика за колачиња", cookieStatus: "Ваш избор",
      cAccepted: "прифатено", cDeclined: "одбиено", cNone: "сè уште не е избрано",
    },
  };
  const CATEGORIES = [
    "Fine line Stories", "Inner Stories", "Love Stories", "Midfield Stories",
    "Neighborhood Stories", "New World Stories", "Outer Stories", "Rhythmic Stories",
  ];
  let locale = "en";
  const t = (k) => DICT[locale][k];

  /* ============ placeholder data ============ */
  const rand = (e) => { const v = 43758.5453 * Math.sin(12.9898 * e); return v - Math.floor(v); };
  const ARTWORKS = [
    "Smrt Kuzmanova (The Death of Kuzman)", "Temptation", "Heaven and Hell",
    "Black Cradle of Bright Life (Cradle of Bright Light)", "Woman in a Garden", "The Birth of Planets",
    "Cosmic Cathedral", "Green Oasis", "Hovering Vision", "Sunset Garden",
    "The Sadness and Tears of Czar Samuel in Full Bloom", "Composition (Petite réalité)", "Heart of Life",
    "Small Goodnesses", "Lake Bride", "Pearls of Youth", "Prohibited Thoughts", "Woman with White Desires",
    "Adam and Eve", "Playful Girls", "Rollicking Whirlwind", "Vegetation", "Atlantis", "Lacustrine Galaxy",
    "White Galaxy", "Galaxy from the Other Side of the Sun", "Horizontal Galaxy", "Spiral Galaxy",
    "Blue Rock", "Warm Lava",
    // further works by Vangel Naumovski
    "Cosmic Egg", "Rascvetane Vatre (Blossoming Fires)", "Green Dreams", "Composition (1968)",
    "Untitled (1966)", "Composition (1972)", "Untitled (1974)", "July Games", "Wilder Ort (Wild Place)",
    "Untitled (1983)", "Surrealist Composition", "Abstract Composition", "Untitled", "Untitled",
    "Untitled", "Untitled", "Auto Portret (Self-Portrait)", "Monogram",
  ];

  function placeholderSrc(i) {
    const h = Math.floor(rand(i + 0.37) * 360);
    const sat = 30 + Math.floor(rand(i + 1.7) * 35);
    const light = 72 + Math.floor(rand(i + 2.3) * 16);
    const bg = `hsl(${h} ${sat}% ${light}%)`;
    const fg = `hsl(${(h + 25) % 360} ${sat - 8}% ${light - 26}%)`;
    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="512" height="512" fill="${bg}"/><circle cx="256" cy="214" r="86" fill="${fg}"/><path d="M92 512c0-104 74-176 164-176s164 72 164 176z" fill="${fg}"/></svg>`;
    return "data:image/svg+xml;charset=utf-8," + encodeURIComponent(svg);
  }

  const works = Array.from({ length: COUNT }, (_, i) => {
    const id = i + 1;
    const title = ARTWORKS[i % ARTWORKS.length]; // the 48 titles repeat across all 197 boxes
    return {
      id,
      name: title,
      fullName: title,
      category: CATEGORIES[Math.floor(rand(id + 5.5) * CATEGORIES.length)],
      // images/01.jpg … images/48.jpg, numbered as in the ARTWORKS list
      src: `${ASSET_BASE}images/${String((i % ARTWORKS.length) + 1).padStart(2, "0")}.jpg`,
      fallback: placeholderSrc(id),
    };
  });

  /* ============ DOM: grid items ============ */
  const content = document.getElementById("content");
  const draggable = document.getElementById("draggable");

  function cardHTML(w) {
    return `<div class="card">
      <div class="cap"><div class="cap-line"><span class="cap-num">${w.id}.</span><span class="cap-name">${w.name}</span></div></div>
      <div class="media"><div class="media-inner"><img alt="${w.name}" src="${w.src}" data-fallback="${w.fallback}" width="512" height="512" draggable="false" decoding="async"></div></div>
    </div>`;
  }
  const frag = document.createDocumentFragment();
  works.forEach((w, i) => {
    const a = document.createElement("a");
    a.className = "gi";
    a.href = `#/artwork/${((w.id - 1) % ARTWORKS.length) + 1}`;
    a.draggable = false;
    a.dataset.index = i;
    a.innerHTML = cardHTML(w);
    frag.appendChild(a);
  });
  FILL_IDS.forEach((id, slot) => {
    const w = works.find((x) => x.id === id);
    const a = document.createElement("a");
    a.className = "gi is-fill";
    a.href = `#/artwork/${((w.id - 1) % ARTWORKS.length) + 1}`;
    a.draggable = false;
    a.dataset.fillSlot = slot;
    a.innerHTML = cardHTML(w);
    frag.appendChild(a);
  });
  draggable.appendChild(frag);

  // a missing image file falls back to its placeholder
  const useFallback = (e) => {
    const img = e.target;
    if (img.tagName === "IMG" && img.dataset.fallback && img.src !== img.dataset.fallback) img.src = img.dataset.fallback;
  };
  draggable.addEventListener("error", useFallback, true);

  /* ============ infinite draggable canvas ============ */
  const remPx = (v) => v * parseFloat(getComputedStyle(document.documentElement).fontSize);
  let frozen = true;          // true during the entrance reveal
  let locked = false;         // input disabled while the search overlay is open
  const items = [];
  const pos = { x: 0, y: 0 };       // current (smoothed) offset
  const target = { x: 0, y: 0 };    // target offset
  const ptr = { x: .5, y: .5 };     // smoothed pointer (0..1) for parallax
  const ptrTarget = { x: .5, y: .5 };
  const downAt = { x: 0, y: 0 };
  const startTarget = { x: 0, y: 0 };
  const tile = { w: 0, h: 0 };
  let vw = 0, vh = 0, pointerId = null, pressing = false, dragging = false;
  let holdTimer = 0, measured = false, raf = 0;

  const wake = () => { raf || (raf = requestAnimationFrame(tick)); };
  const setPointer = (x, y) => { ptrTarget.x = x / vw; ptrTarget.y = y / vh; };
  const moved = (a, b, eps) => Number.isNaN(b) || Math.abs(a - b) > eps;

  const zoomOut = () => {
    if (vw < BREAKPOINT) return;
    gsap.to(draggable, { scale: .8, duration: .4, ease: "power2.out", overwrite: true });
  };

  function place() {
    const sc = gsap.getProperty(draggable, "scale") || 1;
    const k = 0.8;
    const mx = (vw / sc - vw) * .5 + vw * k;
    const my = (vh / sc - vh) * .5 + vh * k;
    for (const e of items) {
      const px = frozen ? 0 : -((ptr.x - .5) * e.w * 1.6);
      const py = frozen ? 0 : -((ptr.y - .5) * e.h * 1.6);
      let x = e.x + pos.x + e.extraX + px;
      let y = e.y + pos.y + e.extraY + py;
      if (!frozen) {
        if (x > vw + mx) { e.extraX -= tile.w; x -= tile.w; }
        else if (x + e.w < -mx) { e.extraX += tile.w; x += tile.w; }
        if (y > vh + my) { e.extraY -= tile.h; y -= tile.h; }
        else if (y + e.h < -my) { e.extraY += tile.h; y += tile.h; }
      }
      if (moved(x, e.tx, .01) || moved(y, e.ty, .01)) {
        e.tx = x; e.ty = y;
        e.el.style.transform = `translate3d(${x}px, ${y}px, 0)`;
      }
    }
  }

  function tick() {
    raf = 0;
    ptr.x += (ptrTarget.x - ptr.x) * .08;
    ptr.y += (ptrTarget.y - ptr.y) * .08;
    pos.x += (target.x - pos.x) * .08;
    pos.y += (target.y - pos.y) * .08;
    place();
    if (pressing || Math.abs(target.x - pos.x) > .05 || Math.abs(target.y - pos.y) > .05 ||
        Math.abs(ptrTarget.x - ptr.x) > .001 || Math.abs(ptrTarget.y - ptr.y) > .001) wake();
  }

  function measure() {
    const mobile = window.innerWidth < BREAKPOINT;
    const L = mobile ? LAYOUT_REM.mobile : LAYOUT_REM.desktop;
    const g = {
      size: remPx(mobile ? ITEM_REM.mobile : ITEM_REM.desktop),
      title: remPx(L.title), gapY: remPx(L.gapY), gapX: remPx(L.gapX),
      rowOffset: remPx(L.rowOffset), shiftY: remPx(L.shiftY), top: remPx(L.top), side: remPx(L.side),
    };
    const rows = Math.ceil(COUNT / PER_ROW);
    const cols = Math.min(PER_ROW, COUNT);
    const rowH = g.size + g.title + g.shiftY + g.gapY;
    const tileW = 2 * g.side + g.rowOffset + cols * g.size + Math.max(cols - 1, 0) * g.gapX;
    const tileH = rows * rowH;

    // base positions: staggered columns, every other row nudged right, last row centred
    const lastRow = Math.floor((COUNT - 1) / PER_ROW);
    const inLast = COUNT % PER_ROW || PER_ROW;
    const lastPad = inLast < PER_ROW ? ((PER_ROW - inLast) * (g.size + g.gapX)) / 2 : 0;
    const base = [];
    for (let n = 0; n < COUNT; n++) {
      const r = Math.floor(n / PER_ROW), c = n % PER_ROW;
      base.push({
        x: g.side + (r === lastRow ? lastPad : 0) + (r % 2 === 0 ? g.rowOffset : 0) + c * (g.size + g.gapX),
        y: g.top + r * rowH + (c % 2 === 0 ? g.shiftY : 0),
      });
    }
    // three duplicate cards fill the gaps of the short last row
    const fillPos = (slot) => {
      const cols = [0.5, 18.5, 19.5];
      return {
        x: g.side + (lastRow % 2 === 0 ? g.rowOffset : 0) + cols[slot] * (g.size + g.gapX),
        y: g.top + lastRow * rowH + ([false, false, true][slot] ? g.shiftY : 0),
      };
    };

    vw = window.innerWidth; vh = window.innerHeight;
    tile.w = tileW; tile.h = tileH;
    items.length = 0;
    for (const el of draggable.querySelectorAll(".gi")) {
      const slot = el.dataset.fillSlot;
      const p = slot === undefined ? base[Number(el.dataset.index)] : fillPos(Number(slot));
      items.push({ el, x: p.x, y: p.y, w: el.offsetWidth, h: el.offsetHeight, extraX: 0, extraY: 0, tx: NaN, ty: NaN });
    }
    if (!measured) {
      // centre the whole sheet in the viewport on first layout
      let x0 = Infinity, y0 = Infinity, x1 = -Infinity, y1 = -Infinity;
      for (const e of items) { x0 = Math.min(x0, e.x); y0 = Math.min(y0, e.y); x1 = Math.max(x1, e.x + e.w); y1 = Math.max(y1, e.y + e.h); }
      const c = { x: .5 * vw - (x0 + x1) * .5, y: .5 * vh - (y0 + y1) * .5 };
      pos.x = target.x = c.x; pos.y = target.y = c.y;
    }
    measured = true;
    for (const e of items) {
      e.extraX = -tile.w * Math.round((e.x + pos.x + .5 * e.w - .5 * vw) / tile.w);
      e.extraY = -tile.h * Math.round((e.y + pos.y + .5 * e.h - .5 * vh) / tile.h);
    }
    place();
  }

  content.addEventListener("wheel", (e) => {
    if (frozen || locked) return;
    e.preventDefault();
    target.x -= .8 * e.deltaX;
    target.y -= .8 * e.deltaY;
    wake();
  }, { passive: false });

  content.addEventListener("pointerdown", (e) => {
    if (frozen || locked) return;
    if (e.pointerType === "mouse" && e.button !== 0) return;
    pointerId = e.pointerId;
    pressing = true;
    dragging = false;
    downAt.x = e.clientX; downAt.y = e.clientY;
    startTarget.x = target.x; startTarget.y = target.y;
    setPointer(e.clientX, e.clientY);
    content.style.cursor = "grabbing";
    clearTimeout(holdTimer);
    holdTimer = setTimeout(() => { if (pressing && !dragging) { dragging = true; zoomOut(); } }, 300);
    wake();
  });

  window.addEventListener("pointermove", (e) => {
    if (frozen || locked) return;
    if (pointerId !== null && e.pointerId === pointerId) { setPointer(e.clientX, e.clientY); wake(); }
    else if (e.pointerType === "mouse") { setPointer(e.clientX, e.clientY); wake(); }
    if (!pressing || e.pointerId !== pointerId) return;
    const dx = e.clientX - downAt.x, dy = e.clientY - downAt.y;
    if (!dragging) {
      if (Math.hypot(dx, dy) <= 8) return;
      clearTimeout(holdTimer);
      dragging = true;
      zoomOut();
    }
    const mult = vw < BREAKPOINT ? 2.6 : 1;
    target.x = startTarget.x + dx * mult;
    target.y = startTarget.y + dy * mult;
    wake();
  });

  const release = (e) => {
    if (frozen || locked || e.pointerId !== pointerId) return;
    clearTimeout(holdTimer);
    pressing = false;
    pointerId = null;
    content.style.cursor = "grab";
    if (dragging) gsap.to(draggable, { scale: 1, duration: .4, ease: "power2.inOut", overwrite: true });
  };
  window.addEventListener("pointerup", release);
  window.addEventListener("pointercancel", release);
  window.addEventListener("resize", measure);

  // a drag (or long press) must not open the card underneath
  content.addEventListener("click", (e) => {
    if (dragging) { e.preventDefault(); e.stopPropagation(); dragging = false; return; }
    const a = e.target.closest(".gi");
    if (a) { e.preventDefault(); navigate(a.getAttribute("href"), a); }
  }, true);

  /* ============ entrance reveal ============ */
  function visibleByDistance() {
    const cx = .5 * vw, cy = .5 * vh;
    return [...draggable.querySelectorAll(".gi")].map((el) => {
      const r = el.getBoundingClientRect();
      return {
        item: el, card: el.querySelector(".card"), x: r.left, y: r.top,
        dist: Math.hypot(r.left + .5 * r.width - cx, r.top + .5 * r.height - cy),
        visible: r.right > -r.width && r.bottom > -r.height && r.left < vw + r.width && r.top < vh + r.height,
      };
    }).filter((e) => e.visible).sort((a, b) => a.dist - b.dist);
  }

  function reveal() {
    const D = visibleByDistance();
    const core = D.filter((e) => e.item.dataset.fillSlot === undefined).slice(0, 13).sort((a, b) => a.y - b.y || a.x - b.x);
    const coreSet = new Set(core.map((e) => e.item));
    const rest = D.filter((e) => !coreSet.has(e.item));
    const m = core[0].item.offsetWidth, p = core[0].item.offsetHeight;

    // diamond of 1-3-5-3-1 slots around the centre
    const cx = .5 * vw, cy = .5 * vh, step = .8 * m;
    const fit = Math.min(1, (.92 * vw) / (4 * step + m));
    let slots = [];
    DIAMOND.forEach((n, row) => {
      for (let k = 0; k < n; k++) {
        const x = cx + (k - (n - 1) * .5) * step * fit - .5 * m;
        const y = cy + (row - 2) * step * fit - .5 * p;
        slots.push({ x, y, dist: Math.hypot(x + .5 * m - cx, y + .5 * p - cy) });
      }
    });
    slots = slots.sort((a, b) => a.dist - b.dist).slice(0, core.length).sort((a, b) => a.y - b.y || a.x - b.x);
    const startScale = .6 * fit;

    // assign cards to slots minimising travel (pairwise swap until stable)
    const assign = slots.map((_, i) => i);
    const d = (i, j) => Math.hypot(core[i].x - slots[j].x, core[i].y - slots[j].y);
    for (let changed = true; changed;) {
      changed = false;
      for (let i = 0; i < assign.length; i++)
        for (let j = i + 1; j < assign.length; j++)
          if (d(i, assign[j]) + d(j, assign[i]) < d(i, assign[i]) + d(j, assign[j])) {
            [assign[i], assign[j]] = [assign[j], assign[i]]; changed = true;
          }
    }

    gsap.set(D.map((e) => e.card), { autoAlpha: 0 });
    core.forEach((e, i) => { const s = slots[assign[i]]; gsap.set(e.item, { x: s.x, y: s.y, scale: startScale }); });
    // everything else starts pushed out to the viewport edge, along the ray from the centre
    rest.forEach((e) => {
      const ix = e.x + .5 * m - cx, iy = e.y + .5 * p - cy;
      const len = Math.hypot(ix, iy) || 1, ux = ix / len, uy = iy / len;
      const dist = Math.min(
        ux > 0 ? (vw - e.x) / ux : ux < 0 ? -(e.x + m) / ux : Infinity,
        uy > 0 ? (vh - e.y) / uy : uy < 0 ? -(e.y + p) / uy : Infinity,
      ) + .5 * m;
      gsap.set(e.item, { x: e.x + ux * dist, y: e.y + uy * dist });
    });

    content.classList.remove("is-invisible");

    const tl = gsap.timeline({ onComplete: revealDone });
    let at = .5;
    gsap.utils.shuffle([...core]).forEach((e) => {
      at += gsap.utils.random(.06, .14);
      tl.set(e.card, { autoAlpha: 1 }, at);
    });
    const spread = at + .8;
    core.forEach((e) => {
      tl.to(e.item, { x: e.x, y: e.y, scale: 1, duration: 1.8, ease: "expo.inOut" }, spread + gsap.utils.random(0, .1));
    });
    tl.set(D.map((e) => e.card.querySelector(".cap")), { opacity: 1, visibility: "visible" }, spread + .9);
    if (rest.length) {
      tl.set(rest.map((e) => e.card), { autoAlpha: 1 }, 0);
      const t0 = spread + .1, d0 = rest[0].dist, span = (rest.at(-1)?.dist ?? 0) - d0 || 1;
      rest.forEach((e) => {
        tl.to(e.item, { x: e.x, y: e.y, duration: 1.8, ease: "expo.inOut" }, t0 + ((e.dist - d0) / span) * .4 + gsap.utils.random(0, .08));
      });
    }
    tl.set(D.map((e) => e.card), { clearProps: "opacity,visibility" });
    tl.call(() => document.body.classList.add("ui-visible"), [], Math.max(0, tl.duration() - 1));
  }

  function revealDone() {
    content.classList.add("captions-ready");
    content.classList.remove("no-pointer");
    content.style.cursor = "grab";
    frozen = false;
    // hand transforms back to the canvas loop
    for (const e of items) { e.el.style.transform = ""; e.tx = NaN; e.ty = NaN; }
    place();
    wake();
  }

  /* ============ filters + search overlay ============ */
  const searchUI = document.getElementById("search-ui");
  const filtersBtn = document.getElementById("filters-btn");
  const searchField = document.getElementById("search-field");
  const input = document.getElementById("search-input");
  const clearInput = document.getElementById("clear-input");
  const openSearchBtn = document.getElementById("open-search");
  const closeSearchBtn = document.getElementById("close-search");
  const cats = document.getElementById("cats");
  const catsInner = document.getElementById("cats-inner");
  const backdrop = document.getElementById("s-backdrop");
  const results = document.getElementById("s-results");
  const resultsInner = document.getElementById("s-results-inner");

  const norm = (s) => s.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase().trim();
  const S = { open: false, searching: false, filtersOpen: false, query: "", debounced: "", selected: [] };
  let debounceTimer = 0;
  // search matches titles that start with what was typed (ignoring case and accents)
  const startingWith = (pool, q) => (q ? pool.filter((w) => w.searchName.startsWith(q)) : pool);

  function filteredWorks() {
    const seen = new Set();
    const pool = (S.selected.length ? works.filter((w) => S.selected.includes(w.category)) : works)
      .filter((w) => { // each artwork once, not once per grid box
        const art = (w.id - 1) % ARTWORKS.length;
        return !seen.has(art) && seen.add(art);
      })
      .map((w) => ({ ...w, searchName: norm(w.fullName) }));
    return startingWith(pool, norm(S.debounced));
  }

  function renderResults() {
    const list = filteredWorks();
    if (!list.length) {
      resultsInner.innerHTML = `<div class="s-empty"><p>${t("searchEmpty")}</p><button type="button" class="text-sm uppercase" data-back>${t("backToGrid")}</button></div>`;
      resultsInner.querySelector("[data-back]").onclick = closeAll;
    } else {
      resultsInner.innerHTML = `<ul class="s-list">${list.map((w) => `
        <li data-search-item><a class="s-item group-item" href="#/artwork/${((w.id - 1) % ARTWORKS.length) + 1}">
          <div class="s-thumb"><img alt="" src="${w.src}" data-fallback="${w.fallback}" width="128" height="128"></div>
          <div class="s-text"><p class="s-name">${w.fullName}</p></div>
          <span class="s-more lbl-row text-sm uppercase"><span class="lbl-bracket">[</span><span class="lbl"><span>${t("knowMore")}</span></span><span class="lbl-bracket">]</span></span>
        </a></li>`).join("")}</ul>`;
    }
    results.scrollTop = 0;
  }

  function renderCats() {
    catsInner.innerHTML = CATEGORIES.map((c) => `
      <button type="button" class="cat ${S.selected.includes(c) ? "is-checked" : ""}" data-cat="${c}">
        <span class="check">[<svg width="4" height="4" viewBox="0 0 4 4" fill="currentColor" aria-hidden="true"><rect width="4" height="4"/></svg>]</span><span>${c}</span>
      </button>`).join("");
  }

  function renderSearchUI() {
    filtersBtn.textContent = `${t("filters")} ${S.filtersOpen ? "[-]" : "[+]"}`;
    filtersBtn.setAttribute("aria-expanded", String(S.filtersOpen));
    searchUI.classList.toggle("is-open", S.open);
    searchField.classList.toggle("is-active", S.searching);
    openSearchBtn.hidden = S.searching;
    closeSearchBtn.hidden = !S.searching;
    clearInput.hidden = !S.query;
    cats.hidden = !S.open;
    cats.classList.toggle("is-open", S.filtersOpen);
    results.classList.toggle("filters-open", S.filtersOpen);
  }

  function openOverlay() {
    if (S.open) return;
    S.open = true;
    locked = true;
    backdrop.hidden = false;
    results.hidden = false;
    renderCats();
    renderResults();
    renderSearchUI();
    gsap.fromTo(backdrop, { autoAlpha: 0 }, { autoAlpha: 1, duration: .5, ease: "power2.out" });
    gsap.set(results, { autoAlpha: 1 });
    const lis = results.querySelectorAll("[data-search-item]");
    gsap.fromTo(lis.length ? lis : resultsInner, { autoAlpha: 0, y: 32 },
      { autoAlpha: 1, y: 0, duration: .8, stagger: .07, ease: "power3.out", delay: .12 });
  }

  function closeAll() {
    const reset = () => {
      Object.assign(S, { open: false, searching: false, filtersOpen: false, query: "", debounced: "", selected: [] });
      input.value = "";
      backdrop.hidden = true;
      results.hidden = true;
      resultsInner.innerHTML = "";
      renderSearchUI();
      locked = false;
    };
    gsap.timeline({ onComplete: reset })
      .to(results, { autoAlpha: 0, duration: .22, ease: "power2.in" })
      .to(backdrop, { autoAlpha: 0, duration: .28, ease: "power2.in" }, 0);
  }

  filtersBtn.addEventListener("click", () => {
    if (!S.open) { S.filtersOpen = true; openOverlay(); return; }
    if (S.filtersOpen) { if (S.searching) { S.filtersOpen = false; renderSearchUI(); } else closeAll(); }
    else { S.filtersOpen = true; renderSearchUI(); }
  });
  openSearchBtn.addEventListener("click", () => {
    S.searching = true;
    if (!S.open) openOverlay(); else renderSearchUI();
    requestAnimationFrame(() => input.focus());
  });
  closeSearchBtn.addEventListener("click", closeAll);
  backdrop.addEventListener("click", closeAll);
  clearInput.addEventListener("click", () => {
    input.value = ""; S.query = ""; S.debounced = "";
    renderSearchUI(); renderResults();
    requestAnimationFrame(() => input.focus());
  });
  input.addEventListener("input", () => {
    S.query = input.value;
    renderSearchUI();
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => { S.debounced = S.query; if (S.open) renderResults(); }, 250);
  });
  catsInner.addEventListener("click", (e) => {
    const b = e.target.closest("[data-cat]");
    if (!b) return;
    const c = b.dataset.cat;
    S.selected = S.selected.includes(c) ? S.selected.filter((x) => x !== c) : [...S.selected, c];
    b.classList.toggle("is-checked", S.selected.includes(c));
    renderResults();
  });
  window.addEventListener("keydown", (e) => { if (e.key === "Escape" && S.open) closeAll(); });
  results.addEventListener("click", (e) => {
    const item = e.target.closest(".s-item");
    if (item) { e.preventDefault(); closeAll(); navigate(item.getAttribute("href")); }
  });
  results.addEventListener("error", useFallback, true);

  /* ============ language switch ============ */
  function applyLocale() {
    document.documentElement.lang = locale;
    document.querySelectorAll("[data-i18n]").forEach((el) => { el.textContent = t(el.dataset.i18n); });
    document.querySelectorAll(".lang [data-locale]").forEach((a) => {
      a.querySelector(".lbl").classList.toggle("is-current", a.dataset.locale === locale);
      a.toggleAttribute("aria-current", a.dataset.locale === locale);
    });
    document.title = `${ARTWORKS.length} ${t("titleSuffix")}`;
    document.querySelectorAll("[data-count]").forEach((el) => { el.textContent = ARTWORKS.length; });
    if (!artId) document.querySelector(".logo .tabular").textContent = ARTWORKS.length;
    renderSearchUI();
    if (S.open) { renderCats(); renderResults(); }
    renderListBar();
    if (listBuilt) { renderListCats(); renderList(false); }
    if (view === "artwork" && artId) renderArtwork(artId);
    if (view === "about") { renderAboutText(); renderAboutSearch(); renderAboutList(); sizeAboutDesk(); measureAbout(); updateAbout(window.scrollY); }
    if (isPage(view)) rerenderPage();
  }
  document.querySelectorAll(".lang [data-locale]").forEach((a) => a.addEventListener("click", (e) => {
    e.preventDefault();
    if (a.dataset.locale !== locale) { locale = a.dataset.locale; applyLocale(); rememberLang(); }
  }));

  /* ============ solid black hover fill for labels in the blended layers ============ */
  document.querySelectorAll("#navbar .group, #search-ui .group, .lang .group").forEach((group) => {
    const lbl = group.querySelector(".lbl");
    if (!lbl) return;
    let chip = null;
    group.addEventListener("mouseenter", () => {
      if (lbl.classList.contains("is-current")) return;
      if (!chip) {
        chip = document.createElement("span");
        chip.className = "lbl hover-chip text-sm uppercase";
        chip.setAttribute("aria-hidden", "true");
        document.body.appendChild(chip);
      }
      chip.innerHTML = lbl.innerHTML;
      const r = lbl.getBoundingClientRect();
      Object.assign(chip.style, { left: `${r.left}px`, top: `${r.top}px`, width: `${r.width}px`, height: `${r.height}px` });
      chip.getBoundingClientRect(); // commit start state so the wipe animates
      chip.classList.add("is-on");
    });
    group.addEventListener("mouseleave", () => chip?.classList.remove("is-on"));
  });

  /* ============ mobile menu ============ */
  const menu = document.getElementById("mobile-menu");
  const menuBtn = document.getElementById("menu-btn");
  let menuOpen = false;
  menuBtn.addEventListener("click", () => {
    menuOpen = !menuOpen;
    menuBtn.dataset.i18n = menuOpen ? "close" : "menu";
    menuBtn.textContent = t(menuBtn.dataset.i18n);
    if (menuOpen) {
      menu.style.display = "flex";
      gsap.to(menu, { clipPath: "polygon(-1% 0%, 101% 0%, 101% 100%, -1% 100%)", duration: .8, ease: "expo.inOut" });
    } else {
      gsap.to(menu, { clipPath: "polygon(101% 0%, 101% 0%, 101% 100%, 101% 100%)", duration: .8, ease: "expo.inOut",
        onComplete: () => { menu.style.display = "none"; gsap.set(menu, { clipPath: "polygon(-1% 0%, -1% 0%, -1% 100%, -1% 100%)" }); } });
    }
  });
  const isRouteLink = (a) => ["#/", "#/archive", "#/gallery", "#/about", "#/team", "#/news", "#/services", "#/contact", "#/cookies"].includes(a.getAttribute("href"));
  document.querySelectorAll('a[href^="#/"]').forEach((a) => {
    if (a.classList.contains("gi")) return;
    a.addEventListener("click", (e) => {
      if (!isRouteLink(a)) { e.preventDefault(); return; }
      if (menuOpen) menuBtn.click();
    });
  });

  /* ============ list view (/archive) ============ */
  const listBody = document.getElementById("list-body");
  const listEl = document.getElementById("l-list");
  const listEmpty = document.getElementById("l-empty");
  const listCount = document.getElementById("l-count");
  const lFilters = document.getElementById("l-filters");
  const lSearch = document.getElementById("l-search");
  const lInput = document.getElementById("l-search-input");
  const lClear = document.getElementById("l-clear");
  const lClose = document.getElementById("l-close");
  const lOpen = document.getElementById("l-open");
  const lCats = document.getElementById("l-cats");
  const lCatsGrid = document.getElementById("l-cats-grid");

  // one row per artwork
  const listWorks = ARTWORKS.map((title, i) => ({
    id: i + 1,
    fullName: title,
    src: `${ASSET_BASE}images/${String(i + 1).padStart(2, "0")}.jpg`,
    fallback: placeholderSrc(i + 1),
    category: CATEGORIES[Math.floor(rand(i + 1 + 5.5) * CATEGORIES.length)],
  }));
  const LS = { filtersOpen: false, searchOpen: false, query: "", debounced: "", selected: [] };
  let listBuilt = false, listObserver = null, listDebounce = 0;

  function listResults() {
    const pool = (LS.selected.length ? listWorks.filter((w) => LS.selected.includes(w.category)) : listWorks)
      .map((w) => ({ ...w, searchName: norm(w.fullName) }));
    return startingWith(pool, norm(LS.debounced));
  }

  function renderListBar() {
    lFilters.textContent = `${t("filters")} ${LS.filtersOpen ? "[-]" : "[+]"}`;
    lFilters.setAttribute("aria-expanded", String(LS.filtersOpen));
    if (LS.filtersOpen) lFilters.dataset.active = "true"; else delete lFilters.dataset.active;
    lSearch.classList.toggle("is-open", LS.searchOpen);
    lOpen.hidden = LS.searchOpen;
    lClose.hidden = !LS.searchOpen;
    lClear.hidden = !LS.query;
    listBody.classList.toggle("filters-open", LS.filtersOpen);
    lCats.classList.toggle("is-open", LS.filtersOpen);
  }

  function renderListCats() {
    lCatsGrid.innerHTML = CATEGORIES.map((c) => `
      <button type="button" class="l-cat ${LS.selected.includes(c) ? "is-checked" : ""}" data-cat="${c}">
        <div class="l-cat-inner"><span class="check">[<svg width="4" height="4" viewBox="0 0 4 4" fill="currentColor" aria-hidden="true"><rect width="4" height="4"/></svg>]</span><span>${c}</span></div>
      </button>`).join("");
  }

  // rows reveal with a random 0-520ms delay as they scroll into view
  function observeReveals() {
    listObserver?.disconnect();
    const els = listEl.querySelectorAll(".reveal-block");
    if (!els.length) return;
    const io = new IntersectionObserver((entries) => {
      for (const en of entries) {
        if (!en.isIntersecting) continue;
        en.target.style.setProperty("--reveal-delay", `${Math.floor(520 * Math.random())}ms`);
        en.target.classList.add("is-inview");
        io.unobserve(en.target);
      }
    }, { root: null, rootMargin: "0px", threshold: 0 });
    listObserver = io;
    requestAnimationFrame(() => els.forEach((el) => io.observe(el)));
  }

  function renderList(reveal = true) {
    const items = listResults();
    listCount.textContent = items.length ? `${items.length} ${items.length === 1 ? t("countOne") : t("countMany")}` : t("noResults");
    if (!items.length) {
      listEl.innerHTML = "";
      listEmpty.hidden = false;
      const q = LS.query.replace(/[<>&"]/g, "");
      listEmpty.innerHTML = `<p>${t("noResults")}${q ? ` ${t("noResultsFor")} <em>${q}</em>` : ""}.</p>` +
        (q || LS.selected.length ? `<div style="margin-top:.75rem"><button type="button" class="lb-btn text-sm" data-clear>${t("clear")}</button></div>` : "");
      listEmpty.querySelector("[data-clear]")?.addEventListener("click", () => {
        Object.assign(LS, { query: "", debounced: "", selected: [], searchOpen: false, filtersOpen: false });
        lInput.value = "";
        renderListBar(); renderListCats(); renderList();
      });
      return;
    }
    listEmpty.hidden = true;
    listEl.innerHTML = items.map((w) => `
      <a class="l-row" href="#/artwork/${w.id}">
        <div class="l-bar" aria-hidden="true"></div>
        <div class="l-text"><span class="reveal-block${reveal ? "" : " is-inview"}">${w.fullName}</span></div>
        <span class="l-divider"></span>
        <img class="l-thumb" src="${w.src}" data-fallback="${w.fallback}" width="256" height="256" alt="">
      </a>`).join("");
    if (reveal) observeReveals();
  }

  lFilters.addEventListener("click", () => { LS.filtersOpen = !LS.filtersOpen; renderListBar(); });
  lCatsGrid.addEventListener("click", (e) => {
    const b = e.target.closest("[data-cat]");
    if (!b) return;
    const c = b.dataset.cat;
    LS.selected = LS.selected.includes(c) ? LS.selected.filter((x) => x !== c) : [...LS.selected, c];
    b.classList.toggle("is-checked", LS.selected.includes(c));
    renderList();
  });
  lOpen.addEventListener("click", () => {
    LS.searchOpen = true; renderListBar();
    requestAnimationFrame(() => lInput.focus());
  });
  lClose.addEventListener("click", () => {
    LS.searchOpen = false; LS.query = ""; LS.debounced = ""; lInput.value = "";
    renderListBar(); renderList();
  });
  lClear.addEventListener("click", () => {
    LS.query = ""; LS.debounced = ""; lInput.value = "";
    renderListBar(); renderList();
    requestAnimationFrame(() => lInput.focus());
  });
  lInput.addEventListener("input", () => {
    LS.query = lInput.value;
    if (LS.query.trim()) LS.searchOpen = true;
    renderListBar();
    clearTimeout(listDebounce);
    listDebounce = setTimeout(() => { LS.debounced = LS.query; renderList(); }, 250);
  });
  listEl.addEventListener("click", (e) => {
    const row = e.target.closest(".l-row");
    if (row) { e.preventDefault(); navigate(row.getAttribute("href")); }
  });
  listEl.addEventListener("error", useFallback, true);

  /* ============ gallery view (/gallery): endless vertical strip of full-screen artworks ============ */
  const galleryMain = document.getElementById("gallery-view");
  const galleryWrap = document.getElementById("g-canvas");
  const galleryLink = document.getElementById("g-link");
  const galleryLabel = document.getElementById("g-label");

  const gallery = (() => {
    const works = listWorks;
    const N = works.length, SLOTS = 7, HALF = 3;
    const mod = (a, b) => ((a % b) + b) % b;
    const damp = (a, b, lambda, dt) => a + (b - a) * (1 - Math.exp(-lambda * dt));
    // load order: nearest to index 0 first, in both directions
    const order = Array.from({ length: N }, (_, i) => i).sort((a, b) => Math.min(a, N - a) - Math.min(b, N - b));

    // each slide: cover-fit, zoom, and a grainy dissolve from a blurred thumb to the sharp image
    const SLIDE_VERT = `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
      }`;
    const SLIDE_FRAG = `
      uniform sampler2D uThumb;
      uniform sampler2D uMap;
      uniform float uSharp;
      uniform float uAlpha;
      uniform float uZoom;
      uniform vec2 uCoverThumb;
      uniform vec2 uCoverSharp;
      varying vec2 vUv;
      float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
      void main() {
        vec2 uvThumb = (vUv - 0.5) * uCoverThumb / uZoom + 0.5;
        vec2 uvSharp = (vUv - 0.5) * uCoverSharp / uZoom + 0.5;
        const float E = 0.35;
        float grain = hash(vUv * 350.0);
        float t = uSharp * (1.0 + 2.0 * E) - E;
        float reveal = smoothstep(grain - E, grain + E, t);
        vec3 color = mix(texture2D(uThumb, uvThumb).rgb, texture2D(uMap, uvSharp).rgb, reveal);
        gl_FragColor = vec4(color, uAlpha);
      }`;
    // whole-screen pass: the picture sinks in the middle and bows against the direction of travel
    const POST_VERT = `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = vec4(position.xy, 0.0, 1.0);
      }`;
    const POST_FRAG = `
      uniform sampler2D uMap;
      uniform float uVelocity;
      uniform float uSink;
      uniform float uBow;
      varying vec2 vUv;
      void main() {
        vec2 centered = vUv * 2.0 - 1.0;
        vec2 sunken = centered * (1.0 + uSink * (1.0 - dot(centered, centered)));
        vec2 uv = mix(vUv, sunken * 0.5 + 0.5, abs(uVelocity));
        float falloff = (1.0 - centered.x * centered.x) * (1.0 - centered.y * centered.y);
        uv.y += uBow * falloff * uVelocity;
        gl_FragColor = vec4(texture2D(uMap, uv).rgb, 1.0);
      }`;

    const drive = { current: 0, target: 0 };
    const size = { w: 0, h: 0, dpr: 1 };
    const thumbs = [], sharp = new Map(), loading = new Set(), failed = new Set();
    const slots = Array.from({ length: SLOTS }, () => ({ item: -1, bound: false, thumbed: false, waited: false, zoomed: false }));
    let renderer, scene, postScene, camera, rt, postMat, meshes = [], mats = [];
    let prevCurrent = 0, velocity = 0, lastH = 0, idle = 0, lastT = 0, raf = 0, running = false;
    let pointerY = 0, index = -1, dragging = false, dragged = false, startY = 0, lastY = 0, lastTime = 0, flick = 0;

    function makeThumb(i) {
      const img = new Image();
      img.onload = () => {
        const c = document.createElement("canvas");
        c.width = c.height = 128;
        const ctx = c.getContext("2d");
        const k = Math.max(128 / img.width, 128 / img.height) * 1.2; // overscan so the blur keeps solid edges
        ctx.filter = "blur(10px)";
        ctx.drawImage(img, (128 - img.width * k) / 2, (128 - img.height * k) / 2, img.width * k, img.height * k);
        const t = new THREE.CanvasTexture(c);
        t.generateMipmaps = false;
        t.minFilter = THREE.LinearFilter;
        thumbs[i] = t;
      };
      img.src = works[i].src;
    }

    function loadSharp(i) {
      loading.add(i);
      new THREE.TextureLoader().load(works[i].src, (t) => {
        loading.delete(i);
        t.generateMipmaps = false;
        t.minFilter = THREE.LinearFilter;
        sharp.set(i, t);
        // keep at most 12 sharp textures, never evicting one that is on screen
        for (const [k, tex] of sharp) {
          if (sharp.size <= 12) break;
          if (!slots.some((s) => s.item === k)) { tex.dispose(); sharp.delete(k); }
        }
      }, undefined, () => { loading.delete(i); failed.add(i); });
    }

    function cover(vec, tex, aspect) {
      if (!tex) return;
      const img = tex.image, a = img.width / img.height;
      aspect > a ? vec.set(1, a / aspect) : vec.set(aspect / a, 1);
    }

    function bind(u, tex, thumbed) {
      const un = mats[u].uniforms;
      un.uMap.value = tex;
      if (thumbed) gsap.fromTo(un.uSharp, { value: 0 }, { value: 1, duration: .8, overwrite: true });
      else {
        un.uSharp.value = 1;
        slots[u].thumbed = true;
        gsap.fromTo(un.uAlpha, { value: 0 }, { value: 1, duration: .4, overwrite: true });
      }
    }

    function init() {
      renderer = new THREE.WebGLRenderer({ antialias: false, alpha: false });
      renderer.setClearColor("#fff", 1);
      galleryWrap.appendChild(renderer.domElement);
      scene = new THREE.Scene();
      postScene = new THREE.Scene();
      camera = new THREE.OrthographicCamera(-1, 1, 1, -1, 0.1, 1000);
      camera.position.z = 10;
      rt = new THREE.WebGLRenderTarget(1, 1, { minFilter: THREE.LinearFilter, magFilter: THREE.LinearFilter });
      const plane = new THREE.PlaneGeometry(1, 1);
      for (let i = 0; i < SLOTS; i++) {
        const m = new THREE.ShaderMaterial({
          vertexShader: SLIDE_VERT, fragmentShader: SLIDE_FRAG, transparent: true,
          uniforms: {
            uThumb: { value: null }, uMap: { value: null }, uSharp: { value: 0 }, uAlpha: { value: 0 },
            uZoom: { value: 1.2 }, uCoverThumb: { value: new THREE.Vector2(1, 1) }, uCoverSharp: { value: new THREE.Vector2(1, 1) },
          },
        });
        const mesh = new THREE.Mesh(plane, m);
        mats.push(m); meshes.push(mesh); scene.add(mesh);
      }
      postMat = new THREE.ShaderMaterial({
        vertexShader: POST_VERT, fragmentShader: POST_FRAG, depthTest: false,
        uniforms: { uMap: { value: null }, uVelocity: { value: 0 }, uSink: { value: .15 }, uBow: { value: .075 } },
      });
      const tri = new THREE.BufferGeometry();
      tri.setAttribute("position", new THREE.BufferAttribute(new Float32Array([-1, -1, 0, 3, -1, 0, -1, 3, 0]), 3));
      tri.setAttribute("uv", new THREE.BufferAttribute(new Float32Array([0, 0, 2, 0, 0, 2]), 2));
      const post = new THREE.Mesh(tri, postMat);
      post.frustumCulled = false;
      postScene.add(post);
      order.forEach((i, k) => setTimeout(() => makeThumb(i), 15 * k));
    }

    function setIndex(i) {
      index = i;
      const w = works[i];
      galleryLink.href = `#/artwork/${w.id}`;
      galleryLink.setAttribute("aria-label", w.fullName);
      galleryLabel.querySelector(".g-num").textContent = w.id;
      galleryLabel.querySelector(".g-name").textContent = w.fullName;
    }

    function frame(now) {
      raf = requestAnimationFrame(frame);
      const dt = Math.min((now - (lastT || now)) / 1000, .1);
      lastT = now;
      const w = galleryMain.clientWidth, h = galleryMain.clientHeight, dpr = Math.min(window.devicePixelRatio || 1, 2);
      const resized = w !== size.w || h !== size.h || dpr !== size.dpr;
      if (resized) {
        Object.assign(size, { w, h, dpr });
        renderer.setPixelRatio(dpr);
        renderer.setSize(w, h);
        Object.assign(camera, { left: -w / 2, right: w / 2, top: h / 2, bottom: -h / 2 });
        camera.updateProjectionMatrix();
        rt.setSize(w * dpr, h * dpr);
      }
      const aspect = w / h;
      if (lastH && h !== lastH) { const k = h / lastH; drive.current *= k; drive.target *= k; prevCurrent *= k; }
      lastH = h;
      drive.current = damp(drive.current, drive.target, 7, dt);
      const speed = (drive.current - prevCurrent) / Math.max(dt, 1e-4); // px per second
      prevCurrent = drive.current;
      velocity = damp(velocity, THREE.MathUtils.clamp(speed / 3500, -1, 1), 5, dt);
      const small = w < BREAKPOINT;
      postMat.uniforms.uVelocity.value = velocity;
      postMat.uniforms.uSink.value = small ? .12 : .15;
      postMat.uniforms.uBow.value = small ? .06 : .075;

      const moving = Math.abs(drive.target - drive.current) > .05 || Math.abs(velocity) > 5e-4;
      let dirty = resized || moving || loading.size > 0;
      const calm = Math.abs(speed) < 2 * h; // only start loading full images when not flying past
      const base = Math.round(drive.current / h);
      for (let r = 0; r < SLOTS; r++) {
        const t = base + r - HALF, u = mod(t, SLOTS), a = mod(t, N);
        const mesh = meshes[u], st = slots[u], un = mats[u].uniforms;
        mesh.position.y = drive.current - t * h;
        mesh.scale.set(w, h, 1);
        cover(un.uCoverThumb.value, un.uThumb.value, aspect);
        cover(un.uCoverSharp.value, un.uMap.value, aspect);
        const onScreen = Math.abs(mesh.position.y) < h;
        if (st.item !== a) {
          dirty = true;
          Object.assign(st, { item: a, bound: false, thumbed: false, waited: false, zoomed: false });
          gsap.killTweensOf([un.uAlpha, un.uSharp, un.uZoom]);
          un.uSharp.value = 0; un.uAlpha.value = 0; un.uZoom.value = 1.2;
        }
        if (onScreen && st.bound && !st.zoomed) {
          dirty = true;
          st.zoomed = true;
          gsap.to(un.uZoom, { value: 1, duration: .8, ease: "power2.out", overwrite: true });
        }
        if (!st.thumbed) {
          dirty = true;
          const tex = thumbs[a];
          if (tex) {
            un.uThumb.value = tex;
            if (st.waited) gsap.fromTo(un.uAlpha, { value: 0 }, { value: 1, duration: .3, overwrite: true });
            else un.uAlpha.value = 1;
            st.thumbed = true;
          } else st.waited = true;
        }
        if (st.bound || failed.has(a)) continue;
        dirty = true;
        const tex = sharp.get(a);
        if (tex) { sharp.delete(a); sharp.set(a, tex); bind(u, tex, st.thumbed); st.bound = true; }
        else if (calm && !loading.has(a)) loadSharp(a);
      }
      // the artwork under the cursor drives the label and the link
      const under = mod(Math.round((drive.current + pointerY - h / 2) / h), N);
      if (under !== index) setIndex(under);

      idle = dirty ? 0 : idle + dt;
      if (idle > 1) return; // nothing has changed for a second: skip drawing
      renderer.setRenderTarget(rt);
      renderer.render(scene, camera);
      renderer.setRenderTarget(null);
      postMat.uniforms.uMap.value = rt.texture;
      renderer.render(postScene, camera);
    }

    // wheel and drag move the strip; a flick keeps it going a little
    galleryMain.addEventListener("wheel", (e) => {
      if (!running || e.ctrlKey) return;
      e.preventDefault();
      drive.target += e.deltaMode === 1 ? 16 * e.deltaY : e.deltaY;
    }, { passive: false });
    galleryMain.addEventListener("pointerdown", (e) => {
      if (!running || !e.isPrimary) return;
      galleryMain.dataset.dragging = "true";
      dragging = true;
      dragged = Math.abs(drive.target - drive.current) > 1; // grabbing a moving strip stops it
      if (dragged) drive.target = drive.current;
      pointerY = e.clientY;
      startY = lastY = e.clientY;
      lastTime = e.timeStamp;
      flick = 0;
    });
    window.addEventListener("pointermove", (e) => {
      if (!running || !e.isPrimary) return;
      pointerY = e.clientY;
      if (!dragging) return;
      const dy = e.clientY - lastY;
      lastY = e.clientY;
      drive.target -= dy;
      const dtm = e.timeStamp - lastTime;
      if (dtm > 0) flick = -dy / dtm;
      lastTime = e.timeStamp;
      if (Math.abs(e.clientY - startY) > 5) dragged = true;
    });
    window.addEventListener("pointerup", (e) => {
      if (!e.isPrimary) return;
      delete galleryMain.dataset.dragging;
      if (dragging) { dragging = false; drive.target += 200 * flick; }
    });
    galleryLink.addEventListener("click", (e) => { e.preventDefault(); if (!dragged) navigate(galleryLink.getAttribute("href")); });

    // label trails the cursor
    const labelX = gsap.quickTo(galleryLabel, "x", { duration: .35, ease: "power3" });
    const labelY = gsap.quickTo(galleryLabel, "y", { duration: .35, ease: "power3" });
    galleryMain.addEventListener("pointermove", (e) => {
      if (e.pointerType !== "mouse") return;
      labelX(e.clientX + 20); labelY(e.clientY + 20);
    });
    galleryMain.addEventListener("pointerenter", (e) => {
      if (e.pointerType !== "mouse") return;
      gsap.set(galleryLabel, { x: e.clientX + 20, y: e.clientY + 20 });
      gsap.to(galleryLabel, { autoAlpha: 1, duration: .2 });
    });
    galleryMain.addEventListener("pointerleave", () => gsap.to(galleryLabel, { autoAlpha: 0, duration: .2 }));

    return {
      start() {
        if (!renderer) init();
        drive.current = drive.target = prevCurrent = 0;
        velocity = 0; lastT = 0; lastH = 0; idle = 0; index = -1; dragging = false;
        slots.forEach((st) => { st.item = -1; });
        setIndex(0);
        running = true;
        raf = requestAnimationFrame(frame);
        gsap.fromTo(galleryMain, { autoAlpha: 0 }, { autoAlpha: 1, duration: .8, ease: "power2.out" });
      },
      stop() {
        running = false;
        cancelAnimationFrame(raf);
        gsap.set(galleryMain, { clearProps: "opacity,visibility" });
      },
    };
  })();

  /* ============ about view (/about): the artist ============ */
  const ABOUT_TEXT = {
    en: [
      "Vangel Naumovski was born in 1922 in Skrebatno, a village near Ohrid, and spent most of his life beside the lake that would later fill his canvases. He left school after the third grade and worked as a gardener, a farmer and a butcher before serving in the army.",
      "In 1946 he enrolled at the art school in Skopje, but stayed for only a year. Back in Ohrid he worked for thirteen years in a woodcarving workshop, painting in every spare hour. His first solo exhibition opened in Ohrid in 1954.",
      "His early paintings drew on folk tales, village customs and local legends: flat, bright scenes that placed him among the naive painters of Yugoslavia. Works such as The Death of Kuzman, from 1956, belong to this period.",
      "At the start of the 1960s his work changed completely. Figures dissolved into soft, glowing, organic shapes — floating gardens, cosmic eggs, lake brides and galaxies — painted with a smooth, luminous finish that made him one of the most distinctive surrealists of the region.",
      "He held more than thirty solo exhibitions, including shows in Rome, London, Paris and Toronto, and took part in around a hundred group exhibitions. When Salvador Dalí saw his Paris exhibition in 1970, he called Naumovski “a painter from a fairy tale”. In 1969 he received awards from the Association of Fine Artists of Macedonia and from the city of Ohrid.",
      "Naumovski later opened a gallery in his own home in Ohrid, where he died on 13 June 2006. His paintings are held in museums in North Macedonia and abroad. This site gathers the works that can still be traced today, from museum collections, archives and auction records.",
    ],
    mk: [
      "Ванѓел Наумовски е роден во 1922 година во Скребатно, село во близина на Охрид, и поголемиот дел од животот го поминал покрај езерото што подоцна ќе ги исполни неговите платна. Училиштето го напуштил по третото одделение и работел како градинар, земјоделец и месар, пред да отслужи војска.",
      "Во 1946 година се запишал во уметничкото училиште во Скопје, но останал само една година. По враќањето во Охрид, тринаесет години работел во работилница за резба во дрво, сликајќи во секој слободен час. Неговата прва самостојна изложба е отворена во Охрид во 1954 година.",
      "Неговите рани слики црпат од народните приказни, селските обичаи и локалните легенди: рамни, светли сцени што го сместиле меѓу наивните сликари на Југославија. Дела како Смртта на Кузман, од 1956 година, припаѓаат на овој период.",
      "На почетокот на шеесеттите години неговото творештво целосно се променило. Фигурите се растопиле во меки, светлечки, органски форми — лебдечки градини, космички јајца, езерски невести и галаксии — насликани со мазна, сјајна површина што го направила еден од најосебените надреалисти во регионот.",
      "Имал повеќе од триесет самостојни изложби, меѓу кои во Рим, Лондон, Париз и Торонто, и учествувал на околу сто групни изложби. Кога Салвадор Дали ја видел неговата изложба во Париз во 1970 година, го нарекол „сликар од бајка“. Во 1969 година добил награди од Друштвото на ликовните уметници на Македонија и од градот Охрид.",
      "Подоцна Наумовски отворил галерија во својот дом во Охрид, каде што починал на 13 јуни 2006 година. Неговите слики се чуваат во музеи во Северна Македонија и во странство. Оваа страница ги собира делата што и денес можат да се пронајдат — од музејски збирки, архиви и аукциски записи.",
    ],
  };

  // one row per artwork: year (and medium where known), plus where the image was found
  const WIKI = "https://www.wikiart.org/en/vangel-naumovski/";
  const ARCHIVE = "https://archive.org/details/VangelNaumovski1";
  const WATTS = "https://50watts.com/Black-Cradle-of-Bright-Life";
  const LOT = "https://www.invaluable.com/auction-lot/";
  const ARTWORK_INFO = [
    ["1956", "", WIKI + "smrt-kuzmanova-1956"], ["1959", "", ARCHIVE], ["1962", "", ARCHIVE], ["1963", "", WATTS],
    ["1964", "", ARCHIVE], ["1967", "oil", LOT + "vangel-naumovski-the-birth-of-planets-oil-on-canv-430-c-b285f625d3"],
    ["1968", "", WATTS], ["1968", "", WATTS], ["1968", "", WATTS], ["1968", "", WATTS],
    ["1968", "", WIKI + "tuga-i-rascvetane-suze-cara-samuila-1968"],
    ["1970", "oil", LOT + "vangel-naumovski-1924-2006-composition-petite-rea-173-c-447427cb6a"], ["1971", "", ARCHIVE],
    ["1972", "oil", LOT + "vangel-naumowski-small-goodnesses-oelgemaelde-von-81-c-4f949d582f"],
    ["1973", "", WATTS], ["1973", "", WATTS], ["1973", "", WATTS], ["1973", "", ARCHIVE], ["1975", "", ARCHIVE],
    ["1975", "", WATTS], ["1975", "", WATTS], ["1976", "", WIKI + "vegetacija-1976"], ["1977", "", WATTS], ["1977", "", WATTS],
    ["1978", "", ARCHIVE], ["1980", "", WATTS], ["1980", "", WATTS], ["1981", "", WATTS],
    ["1987", "", WIKI + "plava-stena-1987"], ["1987", "", WIKI + "topla-lavina-1987"],
    ["1966", "", WIKI + "cosmic-egg-1966"], ["1969", "", WIKI + "rascetane-vatre-1969"],
    ["1967", "oil", LOT + "vangel-naumovski-1924-2006-green-dreams-49-c-43d455e8ec"],
    ["1968", "oil", LOT + "vangel-naumovski-1924-2006-composition-huile-sur--175-c-65a488ebd2"],
    ["1966", "oil", LOT + "vangel-naumovski-ohne-titel-1966-174-c-6de424f919"],
    ["1972", "oil", LOT + "vangel-naumovski-1924-2006-composition-huile-sur--174-c-c62467bb83"],
    ["1974", "acrylic", LOT + "naumovski-vangel-462-c-bde4657856"],
    ["1983", "oil", LOT + "vangel-naumovski-1924-2006-july-games-50-c-8174a45b72"],
    ["1983", "oil", LOT + "vangel-naumovski-1924-ohrid-2006-wilder-137-c-7ba47dfa43"],
    ["1983", "", ARCHIVE], ["", "", LOT + "vangel-naumovski-1924-ohrid-mazedonien-2006-1324-c-d7cedb7ea3"],
    ["", "serigraph", LOT + "naumovski-vangel-1924-ohrid-2006-abstrakte-kompos-6778-c-6104b0c832"],
    ["", "", ARCHIVE], ["", "", ARCHIVE], ["", "", ARCHIVE], ["", "", ARCHIVE],
    ["", "lithograph", LOT + "vangel-naumovski-auto-portret-color-lithograph-164-c-96f493cbd3"],
    ["", "lithograph", LOT + "vangel-naumovski-monogram-color-lithograph-163-c-3084e48b81"],
  ];
  const MEDIUM = {
    en: { oil: "Oil on canvas", acrylic: "Acrylic on canvas", serigraph: "Colour serigraph", lithograph: "Colour lithograph" },
    mk: { oil: "Масло на платно", acrylic: "Акрилик на платно", serigraph: "Сериграфија во боја", lithograph: "Литографија во боја" },
  };

  const aboutMain = document.getElementById("about-main");
  const aboutFooter = document.getElementById("a-footer");
  const abDesk = document.getElementById("ab-desk");
  const abArticle = document.getElementById("ab-article");
  const abParas = document.getElementById("ab-paras");
  const abMobile = document.getElementById("ab-mobile");
  const aList = document.getElementById("a-list");
  const aEmpty = document.getElementById("a-empty");
  const aCount = document.getElementById("a-count");
  const aSearch = document.getElementById("a-search");
  const aInput = document.getElementById("a-search-input");
  const aClear = document.getElementById("a-clear");
  const aClose = document.getElementById("a-close");
  const aOpen = document.getElementById("a-open");
  const AS = { open: false, query: "", debounced: "" };
  let aDebounce = 0;

  // the mobile layout interleaves the same text and photos, as on the original
  const MOBILE_ORDER = [["p", 0], ["img", 1, "406/306"], ["img", 0, "172/250", true], ["p", 1], ["p", 2], ["img", 2, "348/403"],
    ["p", 3], ["p", 4], ["img", 6, "577/434"], ["img", 3, "458/305"], ["p", 5], ["img", 5, "221/298", true], ["img", 4, "339/226"]];

  function renderAboutText() {
    const paras = ABOUT_TEXT[locale] || ABOUT_TEXT.en;
    abParas.innerHTML = paras.map((p) => `<p>${p}</p>`).join("");
    const figs = [...abDesk.querySelectorAll(".ab-fig img")];
    abMobile.innerHTML = `<h1 class="ab-title">${t("aboutTitle")}</h1>` + MOBILE_ORDER.map(([kind, i, ratio, half]) => kind === "p"
      ? `<article class="text-sm"><div class="text-paint"><p>${paras[i]}</p></div></article>`
      : `<div class="ab-m-img${half ? " is-half" : ""}" style="aspect-ratio:${ratio}"><div class="reveal-image" style="position:absolute;inset:0"><img src="${figs[i].getAttribute("src")}" alt=""></div></div>`
    ).join("");
    abMobile.querySelectorAll(".reveal-image img").forEach(watchLoaded);
  }

  function watchLoaded(img) {
    const mark = () => img.closest(".reveal-image").classList.add("is-loaded");
    img.complete && img.naturalWidth ? mark() : img.addEventListener("load", mark, { once: true });
  }
  abDesk.querySelectorAll(".reveal-image img").forEach(watchLoaded);

  function renderAboutList() {
    const pool = listWorks.map((w, i) => ({ ...w, info: ARTWORK_INFO[i], searchName: norm(w.fullName) }));
    const q = norm(AS.debounced);
    const items = startingWith(pool, q);
    aCount.textContent = q ? (items.length ? `${items.length} ${items.length === 1 ? t("countOne") : t("countMany")}` : t("noResults")) : "";
    aEmpty.hidden = !!items.length;
    if (!items.length) {
      aEmpty.innerHTML = `${t("noResults")} ${t("noResultsFor")} <em>${AS.query.replace(/[<>&"]/g, "")}</em>.`;
      aList.innerHTML = "";
      return;
    }
    aList.innerHTML = items.map((w) => {
      const [year, medium, url] = w.info;
      const meta = [year, medium ? MEDIUM[locale]?.[medium] ?? MEDIUM.en[medium] : ""].filter(Boolean).join(" · ") || "—";
      return `<li class="ab-item"><div class="ab-item-bg" aria-hidden="true"></div>
        <a class="ab-row px-global" href="${url}" target="_blank" rel="noopener noreferrer">
          <span class="ab-name">${w.fullName}</span><span class="ab-meta">${meta}</span><span class="ab-discover">${t("discover")}</span>
        </a></li>`;
    }).join("");
  }

  function renderAboutSearch() {
    aSearch.classList.toggle("is-open", AS.open);
    aOpen.hidden = AS.open;
    aClose.hidden = !AS.open;
    aClear.hidden = !AS.query;
    aInput.placeholder = t("searchWorkPh");
  }
  aOpen.addEventListener("click", () => { AS.open = true; renderAboutSearch(); requestAnimationFrame(() => aInput.focus()); });
  aClose.addEventListener("click", () => { Object.assign(AS, { open: false, query: "", debounced: "" }); aInput.value = ""; renderAboutSearch(); renderAboutList(); });
  aClear.addEventListener("click", () => { AS.query = AS.debounced = ""; aInput.value = ""; renderAboutSearch(); renderAboutList(); aInput.focus(); });
  aInput.addEventListener("input", () => {
    AS.query = aInput.value;
    renderAboutSearch();
    clearTimeout(aDebounce);
    aDebounce = setTimeout(() => { AS.debounced = AS.query; renderAboutList(); }, 250);
  });

  // scroll-driven effects (fed by the smooth scroll): photo parallax, photo reveals, text painting
  let aboutFx = [];
  function measureAbout() {
    const h = innerHeight, y = window.scrollY;
    aboutFx = [];
    abDesk.querySelectorAll(".ab-fig").forEach((el) => {
      el.style.transform = "";
      const r = el.getBoundingClientRect();
      const start = r.top + y, size = r.height, inFold = start < h;
      aboutFx.push({ kind: "parallax", el, speed: parseFloat(el.dataset.speed), inFold,
        from: inFold ? 0 : start - h, to: start + size });
    });
    document.querySelectorAll("#about-view .reveal-image").forEach((el) => {
      const r = el.getBoundingClientRect();
      if (!r.height) return; // the layout that is not showing
      const start = r.top + y, inFold = start < h;
      if (inFold) el.classList.add("is-inview");
      aboutFx.push({ kind: "reveal", el, from: inFold ? 0 : start - h + .3 * h });
    });
    document.querySelectorAll("#about-view .text-paint").forEach((el) => {
      const r = el.getBoundingClientRect();
      if (!r.height) return;
      const top = r.top + y, bottom = top + r.height;
      // paints from "top of the text at 80% of the screen" to "bottom of the text at 80%"
      aboutFx.push({ kind: "paint", el, from: Math.max(0, top - .8 * h), to: bottom - .8 * h });
    });
  }
  function updateAbout(scroll) {
    const h = innerHeight;
    for (const fx of aboutFx) {
      if (fx.kind === "parallax") {
        const p = Math.min(1, Math.max(0, (scroll - fx.from) / (fx.to - fx.from || 1)));
        const v = fx.inFold ? -(p * h * fx.speed) : -((-1 + 2 * p) * h * fx.speed);
        fx.el.style.transform = `translate3d(0, ${v}px, 0)`;
      } else if (fx.kind === "reveal") {
        if (scroll > fx.from) fx.el.classList.add("is-inview");
      } else {
        const p = Math.min(1, Math.max(0, (scroll - fx.from) / (fx.to - fx.from || 1)));
        fx.el.style.setProperty("--paint", `${(p * 100).toFixed(2)}%`);
      }
    }
  }
  function sizeAboutDesk() {
    // keep room for the whole text column even if a translation runs longer
    abDesk.style.minHeight = `${Math.max(2750, abArticle.offsetTop + abArticle.offsetHeight + 200)}px`;
  }
  window.addEventListener("resize", () => { if (view === "about") { sizeAboutDesk(); measureAbout(); updateAbout(window.scrollY); } });

  function enterAbout() {
    document.querySelectorAll("#about-view .reveal-image").forEach((el) => el.classList.remove("is-inview"));
    abDesk.querySelectorAll(".ab-fig").forEach((el) => { el.style.transform = ""; });
    Object.assign(AS, { open: false, query: "", debounced: "" });
    aInput.value = "";
    renderAboutText();
    renderAboutSearch();
    renderAboutList();
    sizeAboutDesk();
    measureAbout();
    updateAbout(0);
  }

  // about exit: the page's sections fade out one after another, then the footer
  function leaveAbout(done) {
    const sections = [...aboutMain.children];
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set([...sections, aboutFooter], { clearProps: "opacity,visibility" });
    } });
    tl.to(sections, { autoAlpha: 0, duration: .3, ease: "linear", stagger: { each: .15, from: "start" } }, 0)
      .to(aboutFooter, { autoAlpha: 0, duration: .3, ease: "linear" }, .15 * Math.max(0, sections.length - 1));
    return tl;
  }

  /* ============ artwork stories (EN / MK) ============ */
  // [story paragraphs, period key, size, extra note] for each artwork, in ARTWORKS order
  const STORIES = [
    { en: ["A young man lies dead before a stone house while the women of the village lament over him and a white horse waits, still saddled. The scene follows the opening of The Serdar, the 1860 poem by Grigor Prličev of Ohrid, in which the brave Kuzman is killed and carried home.",
           "Painted in 1956, it belongs to Naumovski's early folk period, when he told local stories in flat, bright colour, years before his turn to the fantastic."],
      mk: ["Млад човек лежи мртов пред камена куќа, жените од селото го оплакуваат, а белиот коњ, сè уште оседлан, чека. Сцената го следи почетокот на „Сердарот“, поемата од 1860 година на охридјанецот Григор Прличев, во која храбриот Кузман загинува и е донесен дома.",
           "Насликана во 1956 година, припаѓа на раниот фолклорен период на Наумовски, кога ги раскажувал локалните приказни со рамни, светли бои, години пред неговиот пресврт кон фантастичното."],
      period: "naive", note: { en: "Based on Grigor Prličev's poem The Serdar (1860).", mk: "Според поемата „Сердарот“ од Григор Прличев (1860)." } },
    { en: ["A pale, solemn woman stands among towering flowers while a small dark figure lingers at her side. The old theme of temptation, familiar from folk tales and church walls alike, is told here with the directness of a village storyteller.",
           "The strange blooms around her already hint at the invented plants that would soon take over his canvases."],
      mk: ["Бледа, свечена жена стои меѓу високи цвеќиња, додека мала темна фигура се задржува покрај неа. Старата тема на искушението, позната и од народните приказни и од ѕидовите на црквите, тука е раскажана со непосредноста на селски раскажувач.",
           "Чудните цветови околу неа веќе ги најавуваат измислените растенија што наскоро ќе ги преземат неговите платна."],
      period: "naive" },
    { en: ["A figure rests in a hollow of dark earth, between a living landscape above and a glowing red depth below. The painting divides the world into two realms and leaves its sleeper suspended between them.",
           "From 1962, it stands on the threshold between his folk paintings and the soft, organic forms of the years that followed."],
      mk: ["Фигура почива во вдлабнатина од темна земја, меѓу жив предел горе и светлечка црвена длабочина долу. Сликата го дели светот на две царства и својот спијач го остава да лебди меѓу нив.",
           "Од 1962 година, таа стои на прагот меѓу неговите фолклорни слики и меките, органски форми од годините што следеа."],
      period: "transition" },
    { en: ["Bright, almost liquid forms rise out of a deep black ground, as if life were being born from darkness. The title states the idea plainly: the black is not an ending but a cradle.",
           "It is one of the first canvases of his fantastic style, painted in 1963, when figures began to dissolve into pure organic shape."],
      mk: ["Светли, речиси течни форми се издигаат од длабока црна подлога, како животот да се раѓа од темнината. Насловот ја кажува идејата јасно: црното не е крај, туку лулка.",
           "Тоа е едно од првите платна во неговиот фантастичен стил, насликано во 1963 година, кога фигурите почнаа да се растопуваат во чиста органска форма."],
      period: "surreal" },
    { en: ["A woman's form grows out of a garden of swelling bulbs, loops and blossoms until it is hard to tell where she ends and the plants begin.",
           "The garden became one of his lasting motifs, a place where the human body and the natural world share the same soft substance."],
      mk: ["Женска форма израснува од градина од набабрени луковици, јамки и цветови, сè додека не станува тешко да се каже каде завршува таа, а каде почнуваат растенијата.",
           "Градината стана еден од неговите трајни мотиви, место каде човечкото тело и природата ја делат истата мека материја."],
      period: "surreal" },
    { en: ["A blue sphere sits inside a fiery shell, while a fine white plant rises from its centre like a newly sprouted seed. Creation is imagined here as something grown rather than built.",
           "From the late 1960s the cosmos entered his work, and planets, eggs and galaxies began to take the place of village scenes."],
      mk: ["Сина сфера седи во огнена лушпа, додека од нејзиниот центар се издига тенко бело растение, како штотуку никнато семе. Создавањето тука е замислено како нешто што расте, а не што се гради.",
           "Од крајот на шеесеттите години космосот влегува во неговото творештво, а планетите, јајцата и галаксиите го заземаат местото на селските сцени."],
      period: "surreal", size: "71 × 102 cm" },
    { en: ["Tall, curving shapes in green, red and blue rise into a vaulted mass, an architecture made of living matter rather than stone.",
           "Painted in Ohrid, a town of churches and monasteries, the picture turns the idea of a sacred building into an organism floating in space."],
      mk: ["Високи, закривени форми во зелено, црвено и сино се издигаат во засводена маса, архитектура направена од жива материја наместо од камен.",
           "Насликана во Охрид, град на цркви и манастири, сликата ја претвора идејата за свет храм во организам што лебди во просторот."],
      period: "surreal" },
    { en: ["In the middle of a dark field, a pool of water arched over by a blue band shelters islands of growth and small hidden creatures.",
           "The oasis is a refuge: a pocket of life and moisture held safe inside the surrounding night."],
      mk: ["Среде темно поле, базен со вода над кој се свива сина лента засолнува островчиња растителност и мали скриени суштества.",
           "Оазата е засолниште: џеб од живот и влага, безбедно чуван во ноќта што ја опкружува."],
      period: "surreal" },
    { en: ["Red and pink forms float over a bright ground like balloons tied with white threads, weightless and slightly unreal.",
           "The painting has the logic of a dream, in which nothing rests on the ground and everything seems about to drift away."],
      mk: ["Црвени и розови форми лебдат над светла подлога како балони врзани со бели конци, бестежински и малку нестварни.",
           "Сликата ја има логиката на сон, во кој ништо не стои на земја, а сè изгледа како да ќе одлета."],
      period: "surreal" },
    { en: ["A great orange fruit, set on a ring like a dish, opens to reveal a small landscape, a lake and growing plants inside it.",
           "A whole world fits inside one ripe form at the end of the day, an image of abundance and of the evening light over the lake."],
      mk: ["Голем портокалов плод, поставен на прстен како на чинија, се отвора и открива мал предел, езеро и растенија што растат внатре.",
           "Цел свет се собира во една зрела форма на крајот од денот, слика на изобилство и на вечерната светлина над езерото."],
      period: "surreal" },
    { en: ["Tsar Samuel ruled his medieval empire from Ohrid. According to the chronicles, in 1014 he collapsed and died after seeing his army return blinded by the Byzantine emperor Basil II.",
           "Naumovski turns that grief into something living: dark green masses broken by red blooms, tears that blossom instead of fall."],
      mk: ["Цар Самоил го владеел своето средновековно царство од Охрид. Според хрониките, во 1014 година паднал и починал откако ја видел својата војска вратена ослепена од византискиот цар Василиј Втори.",
           "Наумовски ја претвора таа тага во нешто живо: темнозелени маси прекинати со црвени цветови, солзи што цветаат наместо да паѓаат."],
      period: "surreal", note: { en: "Based on the medieval accounts of Tsar Samuel's death in 1014.", mk: "Според средновековните записи за смртта на цар Самоил во 1014 година." } },
    { en: ["A tower of leafy green and red forms grows towards a small sun on a pale blue ground. Its French subtitle, petite réalité, calls it a small reality.",
           "It was painted in 1970, the year of his solo exhibition in Paris, where Salvador Dalí called him a painter from a fairy tale."],
      mk: ["Кула од лиснати зелени и црвени форми расте кон мало сонце на бледосина подлога. Нејзиниот француски поднаслов, petite réalité, ја нарекува мала реалност.",
           "Насликана е во 1970 година, годината на неговата самостојна изложба во Париз, каде Салвадор Дали го нарече сликар од бајка."],
      period: "surreal", size: "65 × 46 cm" },
    { en: ["At the centre of a teeming, dark red landscape lies a pale, heart-like form, wrapped in a band of colour.",
           "Everything around it seems to grow from it or flow towards it, as if the heart were the source that keeps the whole picture alive."],
      mk: ["Во центарот на преполн, темноцрвен предел лежи бледа форма слична на срце, обвиткана со лента од боја.",
           "Сè околу неа изгледа како да расте од неа или да тече кон неа, како срцето да е изворот што ја одржува целата слика жива."],
      period: "surreal" },
    { en: ["On a small island under a clear blue sky, round forms rest gently against each other while a pink bird-like shape passes over them.",
           "Naumovski wrote the title on the canvas in Cyrillic, Мали добрини, with the year '72: a modest name for a quiet, tender scene."],
      mk: ["На мал остров под ведро сино небо, тркалезни форми нежно се потпираат една на друга, додека над нив минува розова форма слична на птица.",
           "Наумовски го напишал насловот на платното на кирилица, „Мали добрини“, со годината '72: скромно име за тивка, нежна сцена."],
      period: "surreal", size: "33 × 45 cm" },
    { en: ["A pink female figure rises among dark blue flowers, as if she had stepped out of deep water.",
           "Naumovski spent his life by Lake Ohrid, and the lake returns again and again in his titles. Here it gives him a bride, half woman and half bloom."],
      mk: ["Розова женска фигура се издига меѓу темносини цветови, како да излегла од длабока вода.",
           "Наумовски го поминал животот покрај Охридското Езеро, а езерото постојано се враќа во неговите наслови. Тука му дава невеста, половина жена, половина цвет."],
      period: "surreal" },
    { en: ["Warm, rounded forms gather like a crowd of mushrooms and shells around a soft human torso.",
           "Ohrid is known for its pearls, and the title may nod to that home treasure, here turned into the bright, unfinished promise of youth."],
      mk: ["Топли, заоблени форми се собираат како толпа печурки и школки околу меко човечко тело.",
           "Охрид е познат по своите бисери, а насловот можеби намигнува кон тоа домашно богатство, тука претворено во светлото, недовршено ветување на младоста."],
      period: "surreal" },
    { en: ["A seated pink figure is surrounded by blue and green shapes that swell and branch around her like thoughts taking form.",
           "The painting turns an inner, private world into a landscape and leaves the viewer to guess which thoughts were forbidden."],
      mk: ["Седната розова фигура е опкружена со сини и зелени форми што набабруваат и се разгрануваат околу неа како мисли што добиваат облик.",
           "Сликата го претвора внатрешниот, приватен свет во предел и му остава на гледачот да погоди кои мисли биле забранети."],
      period: "surreal" },
    { en: ["A woman's figure stands among white, bulb-like forms that seem to rise from her, pale against the warmer colours around them.",
           "The white shapes read as longings given a body, light and soft, drifting upward."],
      mk: ["Женска фигура стои меѓу бели форми слични на луковици што изгледаат како да се издигаат од неа, бледи наспроти потоплите бои околу нив.",
           "Белите форми се читаат како копнежи што добиле тело, лесни и меки, како лебдат нагоре."],
      period: "surreal" },
    { en: ["The first couple appear in a garden that is nothing like the traditional paradise: soft forms, unknown fruits and floating shapes surround them.",
           "Naumovski returns to a story every viewer knows and sets it in his own invented nature."],
      mk: ["Првата двојка се појавува во градина што воопшто не личи на традиционалниот рај: ги опкружуваат меки форми, непознати плодови и лебдечки облици.",
           "Наумовски се враќа на приказна што секој гледач ја знае и ја сместува во својата измислена природа."],
      period: "surreal" },
    { en: ["Two girls stand close together among bright plumes and petals, their bodies flowing into the shapes around them.",
           "It is one of his lightest pictures, full of movement and play."],
      mk: ["Две девојки стојат блиску една до друга меѓу светли пердуви и ливчиња, а нивните тела се претопуваат во формите околу нив.",
           "Ова е една од неговите најлесни слики, полна со движење и игра."],
      period: "surreal" },
    { en: ["Against a dark ground, a figure spins inside a ring of coloured shapes that seem to dance around her.",
           "The whirlwind is joyful rather than threatening: a storm of colour and motion."],
      mk: ["Наспроти темна подлога, фигура се врти во прстен од обоени форми што изгледаат како да танцуваат околу неа.",
           "Вихорот е радосен, а не заканувачки: бура од боја и движење."],
      period: "surreal" },
    { en: ["Flowers, stems and seed-like forms spring up against a deep blue background, each one a plant that exists only in his imagination.",
           "The title is simple, but the picture is a whole invented botany."],
      mk: ["Цвеќиња, стебла и форми слични на семиња никнуваат наспроти длабока сина позадина, секое растение што постои само во неговата имагинација.",
           "Насловот е едноставен, но сликата е цела измислена ботаника."],
      period: "surreal" },
    { en: ["A tall blue tower rises from a still pool, surrounded by rounded bushes under a wide sky.",
           "The sunken city of the old legend is shown at the moment it surfaces, calm and strange, as if it had never left."],
      mk: ["Висока сина кула се издига од мирен базен, опкружена со заоблени грмушки под широко небо.",
           "Потонатиот град од старата легенда е прикажан во мигот кога излегува на површина, мирен и чуден, како никогаш да не заминал."],
      period: "surreal" },
    { en: ["A green, figure-like form floats on a blue-green ground, threaded with fine lines.",
           "'Lacustrine' means 'of lakes': here the lake he lived beside and the galaxies of his later paintings become one."],
      mk: ["Зелена форма слична на фигура лебди на синозелена подлога, протната со тенки линии.",
           "„Лакустрина“ значи „езерска“: тука езерото покрај кое живеел и галаксиите од неговите подоцнежни слики стануваат едно."],
      period: "surreal" },
    { en: ["Light, bird-like shapes in green and blue drift across a pale sky.",
           "In his galaxy pictures, outer space is not dark at all. It is airy and bright, almost like a morning over water."],
      mk: ["Лесни форми слични на птици, во зелено и сино, лебдат по бледо небо.",
           "Во неговите слики со галаксии, вселената воопшто не е темна. Таа е воздушеста и светла, речиси како утро над вода."],
      period: "surreal" },
    { en: ["A golden orb hangs in the light while dark, sweeping forms curve around it.",
           "The title invites us to imagine a galaxy no one can see from Earth, one hidden behind the sun."],
      mk: ["Златна топка виси во светлината, додека темни, замавнати форми се свиткуваат околу неа.",
           "Насловот нè поканува да замислиме галаксија што никој не може да ја види од Земјата, скриена зад сонцето."],
      period: "surreal" },
    { en: ["Across a warm golden ground, a long stream of forms flows sideways around a cup-like shape.",
           "Instead of spiralling, this galaxy stretches out like a river of colour."],
      mk: ["Преку топла златна подлога, долг поток од форми тече настрана околу облик сличен на чаша.",
           "Наместо да се врти во спирала, оваа галаксија се протега како река од бои."],
      period: "surreal" },
    { en: ["Fine threads spiral down through deep blue and black, gathering green forms along the way.",
           "It is one of the last of his galaxy pictures, painted in 1981."],
      mk: ["Тенки конци се спуштаат во спирала низ длабоко сино и црно, собирајќи зелени форми по патот.",
           "Ова е една од последните слики од неговата серија со галаксии, насликана во 1981 година."],
      period: "surreal" },
    { en: ["A loose blue form, painted with free and broken strokes, sits on an open white ground.",
           "In the late 1980s his surfaces became freer and less polished, closer to sketches made in colour."],
      mk: ["Слободна сина форма, насликана со слободни и искршени потези, седи на отворена бела подлога.",
           "На крајот на осумдесеттите години неговите површини станаа послободни и помалку полирани, поблиску до скици направени во боја."],
      period: "late" },
    { en: ["Pink, ochre and green strokes pour across the paper like a slow, warm flow.",
           "It is a late work, painted in 1987 under the original title Topla lavina."],
      mk: ["Розови, окер и зелени потези се слеваат преку хартијата како бавен, топол тек.",
           "Ова е доцно дело, насликано во 1987 година под оригиналниот наслов „Топла лавина“."],
      period: "late" },
    { en: ["A great cracked egg is painted in shades of grey, with clouds gathering inside it.",
           "The cosmic egg is one of the oldest images of creation: a whole universe waiting to hatch."],
      mk: ["Големо напукнато јајце е насликано во нијанси на сиво, а внатре во него се собираат облаци.",
           "Космичкото јајце е една од најстарите слики на создавањето: цел универзум што чека да се изведе."],
      period: "surreal" },
    { en: ["Red, flower-like shapes burn across a dark green field beneath colourful canopies.",
           "The original title, Rascvetane vatre, joins fire and blossom: flames that open like flowers."],
      mk: ["Црвени форми слични на цветови горат низ темнозелено поле под шарени покриви.",
           "Оригиналниот наслов, „Расцветани ватри“, ги спојува огнот и цветот: пламени што се отвораат како цветови."],
      period: "surreal" },
    { en: ["Despite its title, the canvas is dominated by red; the green lives at its heart, in a dark central form.",
           "Naumovski signed it with its original name, Zeleni sni, and the year 1967."],
      mk: ["И покрај насловот, на платното доминира црвеното; зеленото живее во неговото срце, во темна централна форма.",
           "Наумовски ја потпишал со оригиналното име, „Зелени сни“, и годината 1967."],
      period: "surreal", size: "94 × 64 cm" },
    { en: ["Large red and green petal forms open across a bright blue ground.",
           "He signed and titled the canvas in Cyrillic and dated it '68."],
      mk: ["Големи црвени и зелени ливчиња се отвораат преку светла сина подлога.",
           "Тој го потпишал и насловил платното на кирилица и го датирал '68."],
      period: "surreal", size: "81 × 65 cm" },
    { en: ["A white daisy shines in a dark blue landscape next to a flaming red plume.",
           "The canvas is signed and dated 1966, from the years when his new fantastic style was taking shape."],
      mk: ["Бела маргаритка свети во темносин предел до пламено црвен перјаник.",
           "Платното е потпишано и датирано 1966 година, од годините кога неговиот нов фантастичен стил добивал облик."],
      period: "surreal", size: "53 × 43.5 cm" },
    { en: ["Colourful hills and white plumes rise under a soft blue sky.",
           "A small, intimate canvas, signed and titled in Cyrillic and dated '72."],
      mk: ["Шарени ридови и бели перјаници се издигаат под меко сино небо.",
           "Мало, интимно платно, потпишано и насловено на кирилица и датирано '72."],
      period: "surreal", size: "36 × 30 cm" },
    { en: ["Flowers and soft organic forms float on a pale ground.",
           "Painted in acrylic, it is signed and dated 1974."],
      mk: ["Цвеќиња и меки органски форми лебдат на бледа подлога.",
           "Насликана со акрилик, потпишана е и датирана 1974 година."],
      period: "surreal", size: "64 × 52 cm" },
    { en: ["Purple, green and red spheres play across a pale summer sky.",
           "The original title, Julska igra, is written in Cyrillic beside his signature."],
      mk: ["Виолетови, зелени и црвени сфери си играат по бледо летно небо.",
           "Оригиналниот наслов, „Јулска игра“, е напишан на кирилица покрај неговиот потпис."],
      period: "surreal", size: "38 × 46 cm" },
    { en: ["A white arc sweeps over a blue-violet ground scattered with coloured bulbs.",
           "The painting was shown and sold in Germany under the title Wilder Ort, and carries a label from the Galerie Hell & Hell in Munich."],
      mk: ["Бел лак се протега над синовиолетова подлога посеана со обоени луковици.",
           "Сликата е изложена и продадена во Германија под насловот „Wilder Ort“ и носи етикета од галеријата Hell & Hell во Минхен."],
      period: "surreal", size: "25 × 30 cm" },
    { en: ["A softly painted nude stands among flowers in pale green and blue.",
           "The picture is known only by its date, 1983."],
      mk: ["Нежно насликан акт стои меѓу цвеќиња во бледозелено и сино.",
           "Сликата е позната само по својата година, 1983."],
      period: "surreal" },
    { en: ["Blue, green and red forms crowd a dark ground, topped with pale, mushroom-like shapes.",
           "It surfaced at auction in Germany simply as a surrealist composition."],
      mk: ["Сини, зелени и црвени форми се збиваат на темна подлога, а над нив стојат бледи форми слични на печурки.",
           "Се појави на аукција во Германија едноставно како надреалистичка композиција."],
      period: "surreal" },
    { en: ["A screen print in blue and green, built around a pale central figure.",
           "Prints like this one carried his imagery beyond the single canvas."],
      mk: ["Сериграфија во сино и зелено, изградена околу бледа централна фигура.",
           "Графиките како оваа ги пренесуваа неговите слики подалеку од единственото платно."],
      period: "print" },
    { en: ["A bright green landscape is scattered with egg- and disc-like forms under an orange sun.",
           "Its title and date are not recorded."],
      mk: ["Светлозелен предел е посеан со форми слични на јајца и дискови под портокалово сонце.",
           "Неговиот наслов и година не се забележани."],
      period: "" },
    { en: ["Flowering, tree-like forms float in front of blue mountains.",
           "Its title and date are not recorded."],
      mk: ["Расцутени форми слични на дрвја лебдат пред сини планини.",
           "Неговиот наслов и година не се забележани."],
      period: "" },
    { en: ["Grey and red forms sweep loosely across a white ground.",
           "The free, open brushwork recalls his late works of the 1980s."],
      mk: ["Сиви и црвени форми слободно се протегаат преку бела подлога.",
           "Слободниот, отворен потег потсетува на неговите доцни дела од осумдесеттите години."],
      period: "" },
    { en: ["A seated nude rests in a dark landscape, surrounded by coloured forms.",
           "Its title and date are not recorded."],
      mk: ["Седнат акт почива во темен предел, опкружен со обоени форми.",
           "Неговиот наслов и година не се забележани."],
      period: "" },
    { en: ["In this colour lithograph, the artist's own face dissolves into black, red and green shapes around a spiralling eye.",
           "Even his self-portrait takes the form of one of his invented organisms."],
      mk: ["Во оваа литографија во боја, сопственото лице на уметникот се растопува во црни, црвени и зелени форми околу спирално око.",
           "Дури и неговиот автопортрет го добива обликот на еден од неговите измислени организми."],
      period: "print" },
    { en: ["A colour lithograph built from the same forms as his self-portrait, joined by lettering.",
           "It works as a personal emblem: the painter's world turned into a signature."],
      mk: ["Литографија во боја изградена од истите форми како неговиот автопортрет, придружени со букви.",
           "Таа функционира како личен амблем: светот на сликарот претворен во потпис."],
      period: "print" },
  ];

  /* ============ artwork page (/artwork/N) ============ */
  const PERIOD = {
    en: { naive: "Folk period", transition: "Transition", surreal: "Fantastic period", late: "Late work", print: "Print" },
    mk: { naive: "Фолклорен период", transition: "Премин", surreal: "Фантастичен период", late: "Доцно дело", print: "Графика" },
  };
  const SOURCE_NAME = (url) => url.includes("wikiart") ? "WikiArt" : url.includes("archive.org") ? "Internet Archive"
    : url.includes("50watts") ? "50 Watts" : url.includes("invaluable") ? (locale === "mk" ? "Аукциски запис" : "Auction record") : "—";
  // two close-up crops shown under each painting: the point (x %, y %) each one zooms into
  const DETAILS = [[30, 30], [70, 72]];

  const awMain = document.getElementById("aw-main");
  const awContent = document.getElementById("aw-content");
  const awStory = document.getElementById("aw-story");
  const awStoryText = document.getElementById("aw-story-text");
  const awGallery = document.getElementById("aw-gallery");
  const awSheet = document.getElementById("aw-sheet");
  const awNote = document.getElementById("aw-note");
  const awNoteM = document.getElementById("aw-note-m");
  const awArtist = document.getElementById("aw-artist-reveal");
  const awRelated = document.getElementById("aw-related");
  const awTrack = document.getElementById("aw-track");
  const awTrackRow = document.getElementById("aw-track-row");
  const awFooter = document.getElementById("w-footer");
  const logoNum = document.querySelector(".logo .tabular");
  let artId = 0, awDimmed = false;

  document.getElementById("aw-story-btn").addEventListener("click", () => {
    awStory.dataset.state = awStory.dataset.state === "open" ? "closed" : "open";
  });

  // split a paragraph into its rendered lines so each line can be wiped in on its own
  function splitLines(el, html) {
    el.innerHTML = `<p>${html}</p>`;
    const p = el.firstChild;
    const words = p.textContent.split(/\s+/).filter(Boolean);
    p.innerHTML = words.map((w) => `<span class="w">${w} </span>`).join("");
    const lines = [];
    let top = null;
    p.querySelectorAll(".w").forEach((s) => {
      if (s.offsetTop !== top) { lines.push([]); top = s.offsetTop; }
      lines.at(-1).push(s.textContent);
    });
    p.innerHTML = lines.map((l, i) => `<span class="line-mask" style="--line:${i + 1}"><span class="line">${l.join("")}</span></span>`).join("");
  }

  // reveals are triggered by position, checked on entry and on every scroll frame
  let awPending = [];
  const anyVisible = (r, vh) => r.top < vh && r.bottom > 0;
  const halfVisible = (r, vh) => (Math.min(r.bottom, vh) - Math.max(r.top, 0)) / r.height >= .5;
  const intoScreen35 = (r, vh) => r.top < vh * .65 && r.bottom > 0; // 35% into the screen
  function watch(el, test, onIn) { awPending.push({ el, test, onIn }); }
  function checkArtworkReveals() {
    if (!awPending.length) return;
    const vh = innerHeight;
    awPending = awPending.filter((w) => {
      const r = w.el.getBoundingClientRect();
      if (!r.height || !w.test(r, vh)) return true;
      w.onIn(w.el);
      return false;
    });
  }

  function renderArtwork(n) {
    const i = n - 1, w = listWorks[i], s = STORIES[i] || { en: [], mk: [] }, info = ARTWORK_INFO[i];
    const L = locale === "mk" ? "mk" : "en";
    const [year, medium, url] = info;
    logoNum.textContent = String(n).padStart(3, "0");
    document.title = `${w.fullName} (${ARTWORKS.length} ${t("titleSuffix")})`;

    awStoryText.innerHTML = (s[L] || s.en).map((p) => `<p>${p}</p>`).join("");

    const field = (label, value, i2, wide) =>
      `<div class="${wide ? "is-wide" : ""}"><dt>${label}</dt><dd><span class="reveal-block" style="--reveal-delay:${i2 * 80}ms">${value || "—"}</span></dd></div>`;
    awSheet.innerHTML = `<div class="aw-sheet-grid">
      ${field(t("fTitle"), w.fullName, 0, true)}
      ${field(t("fYear"), year, 1)}
      ${field(t("fPeriod"), s.period ? PERIOD[L][s.period] : "", 2)}
      ${field(t("fMedium"), medium ? (MEDIUM[L] || MEDIUM.en)[medium] : "", 3)}
      ${field(t("fSize"), s.size, 4)}
      ${field(t("fSource"), SOURCE_NAME(url), 5)}
    </div>`;

    const media = (inner, extra = "") => `<div class="aw-slide"><div class="aw-media${extra}">
        ${inner}<div class="aw-panel aw-panel-ink"></div><div class="aw-panel aw-panel-paper"></div></div></div>`;
    awGallery.innerHTML =
      media(`<div class="aw-move"><img src="${w.src}" alt="${w.fullName}"></div>`) +
      DETAILS.map(([x, y]) => media(`<div class="aw-move aw-detail"><img src="${w.src}" alt="" style="transform-origin:${x}% ${y}%"></div>`)).join("");

    const noteText = `${s.note ? s.note[L] + " " : ""}${L === "mk" ? "Извор на сликата" : "Image source"}: ${SOURCE_NAME(url)}.`;
    [awNote, awNoteM].forEach((el) => { el.className = el.className.replace(" reveal-lines", "").replace(" is-inview", "") + " reveal-lines"; });

    awTrackRow.innerHTML = listWorks.map((x) => `
      <a class="aw-item${x.id === n ? " is-current" : ""}" data-item data-id="${x.id}" href="#/artwork/${x.id}">
        <div class="aw-item-cap"><span class="aw-item-num">${x.id}.</span><span class="aw-item-name">${x.fullName}${x.id === n
          ? `<span class="aw-item-check">[<svg width="4" height="4" viewBox="0 0 4 4" fill="currentColor"><rect width="4" height="4"/></svg>]</span>` : ""}</span></div>
        <div class="aw-item-img"><img src="${x.src}" alt="${x.fullName}" loading="lazy"></div>
      </a>`).join("");

    requestAnimationFrame(() => {
      splitLines(awNote, noteText);
      splitLines(awNoteM, noteText);
      // the strip starts at the next artwork
      const next = awTrackRow.querySelector(`[data-id="${(n % listWorks.length) + 1}"]`);
      if (next) awTrack.scrollLeft = next.offsetLeft - parseFloat(getComputedStyle(awTrack).paddingLeft);
      startArtworkReveals();
    });
  }

  function startArtworkReveals() {
    awPending = [];
    // images: once half visible and loaded
    awGallery.querySelectorAll(".aw-media").forEach((m) => {
      const img = m.querySelector("img");
      const ready = () => { watch(m, halfVisible, (el) => el.classList.add("is-shown")); checkArtworkReveals(); };
      img.complete && img.naturalWidth ? ready() : img.addEventListener("load", ready, { once: true });
    });
    // facts and the artist name: black-bar reveal as they come into view
    [...awSheet.querySelectorAll(".reveal-block"), awArtist].forEach((el) => {
      el.classList.remove("is-inview");
      watch(el, anyVisible, (x) => x.classList.add("is-inview"));
    });
    // source note: once it is 35% into the screen
    [awNote, awNoteM].forEach((el) => watch(el, intoScreen35, (x) => x.classList.add("is-inview")));
    checkArtworkReveals();
  }

  // dim the page while the strip of other works is in view at the bottom
  function updateArtworkDim() {
    const r = awRelated.getBoundingClientRect();
    const active = r.bottom <= innerHeight + 1 && r.top > 0;
    if (active !== awDimmed) {
      awDimmed = active;
      gsap.to(awContent, { opacity: active ? .3 : 1, duration: .5, ease: "power2.out" });
    }
  }

  function enterArtwork(n) {
    artId = n;
    document.body.classList.add("is-artwork");
    awDimmed = false;
    gsap.set(awContent, { opacity: 1 });
    awStory.dataset.state = "closed";
    renderArtwork(n);
    // the story opens on its own just after the page appears
    requestAnimationFrame(() => requestAnimationFrame(() => { if (artId === n) awStory.dataset.state = "open"; }));
  }

  function exitArtwork() {
    awPending = [];
    document.body.classList.remove("is-artwork");
    logoNum.textContent = ARTWORKS.length;
    document.title = `${ARTWORKS.length} ${t("titleSuffix")}`;
    artId = 0;
  }

  // artwork exit: page, strip and footer fade out one after another
  function leaveArtwork(done) {
    const parts = [awContent, awRelated, awFooter];
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set(parts, { clearProps: "opacity,visibility" });
    } });
    tl.to(parts, { autoAlpha: 0, duration: .3, ease: "linear", stagger: { each: .15, from: "start" } }, 0);
    return tl;
  }

  window.addEventListener("resize", () => { if (view === "artwork") checkArtworkReveals(); });
  awTrackRow.addEventListener("click", (e) => {
    const a = e.target.closest(".aw-item");
    if (!a) return;
    e.preventDefault();
    navigate(a.getAttribute("href"));
  });
  document.querySelector("#aw-artist-reveal a").addEventListener("click", (e) => { e.preventDefault(); navigate("#/about"); });

  /* ============ presentation pages: content (EN / MK) ============ */
  const CONTACT_INFO = {
    email: "contact@vangelnaumovski.example",
    phone: "+389 46 000 000",
  };

  // news, newest first; `art` is the related artwork number
  const NEWS = [
    { slug: "catalogue-online", date: "2026-10-01", art: 7,
      en: { title: "The catalogue is online",
            lead: "Forty-eight paintings by Vangel Naumovski, gathered in one place for the first time.",
            body: ["Today we are opening an online catalogue of the paintings of Vangel Naumovski. It brings together 48 works, from his early folk scenes of the 1950s to the galaxies of the early 1980s and the loose, late works of 1987.",
                   "Every painting has its own page with a short story, the year, the period and the source of the image. You can browse the works in an endless grid, as a list, or one by one in a full-screen gallery.",
                   "The catalogue is available in English and Macedonian. It will keep growing as more works are traced in museums, archives and auction records."] },
      mk: { title: "Каталогот е онлајн",
            lead: "Четириесет и осум слики од Ванѓел Наумовски, за првпат собрани на едно место.",
            body: ["Денес отвораме онлајн каталог на сликите на Ванѓел Наумовски. Тој собира 48 дела, од неговите рани фолклорни сцени од педесеттите, до галаксиите од почетокот на осумдесеттите и слободните, доцни дела од 1987 година.",
                   "Секоја слика има своја страница со кратка приказна, година, период и извор на сликата. Делата може да ги разгледувате во бескрајна мрежа, како листа, или едно по едно во галерија на цел екран.",
                   "Каталогот е достапен на англиски и македонски јазик. Ќе продолжи да расте како што ќе се пронаоѓаат нови дела во музеи, архиви и аукциски записи."] } },
    { slug: "twenty-years", date: "2026-06-13", art: 15,
      en: { title: "Twenty years without Vangel Naumovski",
            lead: "The painter died in Ohrid on 13 June 2006.",
            body: ["Twenty years ago today, Vangel Naumovski died in Ohrid, the town by the lake that runs through so many of his titles: Lake Bride, Lacustrine Galaxy, Atlantis.",
                   "He began as a storyteller of village life and folk legend, and in the early 1960s turned to the soft, glowing forms that made him one of the most distinctive surrealists of the region.",
                   "To mark the anniversary, we invite you to revisit his work, from The Death of Kuzman to Spiral Galaxy."] },
      mk: { title: "Дваесет години без Ванѓел Наумовски",
            lead: "Сликарот почина во Охрид на 13 јуни 2006 година.",
            body: ["Пред точно дваесет години, во Охрид почина Ванѓел Наумовски, во градот покрај езерото што се провлекува низ толку многу негови наслови: Езерска невеста, Езерска галаксија, Атлантида.",
                   "Почнал како раскажувач на селскиот живот и народните легенди, а на почетокот на шеесеттите се свртел кон меките, светлечки форми што го направија еден од најосебените надреалисти во регионот.",
                   "По повод годишнината, ве покануваме повторно да се навратите на неговото творештво, од Смртта на Кузман до Спирална галаксија."] } },
    { slug: "auction-finds", date: "2026-04-20", art: 33,
      en: { title: "From the auction rooms: works that resurfaced",
            lead: "Several paintings in this catalogue are known only from recent sales.",
            body: ["Not every painting by Naumovski hangs in a museum. Some have travelled through private collections and resurfaced at auction, which is often the only place where they can still be seen.",
                   "Green Dreams (1967) and July Games (1983) were offered in Zagreb, Composition (Petite réalité) in France, and an untitled canvas from 1974 in Germany. Wilder Ort, painted in 1983, carries the label of a Munich gallery.",
                   "Each of these works has its own page in the catalogue, with the auction record listed as its source."] },
      mk: { title: "Од аукциските сали: дела што повторно се појавија",
            lead: "Неколку слики од овој каталог се познати само од неодамнешни продажби.",
            body: ["Не секоја слика на Наумовски виси во музеј. Некои патувале низ приватни збирки и повторно се појавиле на аукции, што често е единственото место каде што сè уште може да се видат.",
                   "„Зелени сни“ (1967) и „Јулска игра“ (1983) беа понудени во Загреб, „Композиција (Petite réalité)“ во Франција, а едно ненасловено платно од 1974 година во Германија. „Wilder Ort“, насликана во 1983 година, носи етикета од галерија во Минхен.",
                   "Секое од овие дела има своја страница во каталогот, со аукцискиот запис наведен како извор."] } },
    { slug: "correcting-titles", date: "2026-03-05", art: 26,
      en: { title: "Correcting the record: mislabelled titles",
            lead: "Why some paintings here carry different titles than elsewhere online.",
            body: ["While building the catalogue we compared every image against several sources. For nine paintings, the most widely used online database attached the wrong title to the image.",
                   "The work often shown as Black Cradle of Bright Life, for example, is in fact Galaxy from the Other Side of the Sun, painted in 1980. Two other independent sources agreed with each other on every one of these cases.",
                   "We follow those sources, and we note the remaining works that could only be checked against a single source."] },
      mk: { title: "Исправка на записите: погрешни наслови",
            lead: "Зошто некои слики тука носат поинакви наслови отколку на други места на интернет.",
            body: ["Додека го градевме каталогот, ја споредивме секоја слика со неколку извори. Кај девет слики, најкористената онлајн база на податоци на сликата ѝ припишала погрешен наслов.",
                   "Делото што често се прикажува како „Црна лулка на светлиот живот“, на пример, е всушност „Галаксија од другата страна на сонцето“, насликана во 1980 година. Два други независни извори се совпаѓаа меѓусебно во секој од овие случаи.",
                   "Ги следиме тие извори и ги наведуваме преостанатите дела што можеа да се проверат само со еден извор."] } },
    { slug: "dali-paris-1970", date: "2026-02-10", art: 12,
      en: { title: "When Dalí saw Naumovski: Paris, 1970",
            lead: "A visit that followed the painter for the rest of his life.",
            body: ["In 1970 Naumovski held a solo exhibition in Paris. Among the visitors was Salvador Dalí, who called him a painter from a fairy tale.",
                   "By then Naumovski had left his folk scenes behind. His canvases were filled with glowing organic forms, floating gardens and cosmic shapes, a world that had much in common with surrealism.",
                   "Composition (Petite réalité), painted the same year, gives a sense of the work Paris saw: a small reality growing towards the sun."] },
      mk: { title: "Кога Дали го виде Наумовски: Париз, 1970",
            lead: "Посета што го следеше сликарот до крајот на животот.",
            body: ["Во 1970 година Наумовски имал самостојна изложба во Париз. Меѓу посетителите бил и Салвадор Дали, кој го нарекол сликар од бајка.",
                   "Дотогаш Наумовски веќе ги оставил зад себе фолклорните сцени. Неговите платна биле исполнети со светлечки органски форми, лебдечки градини и космички облици, свет што имал многу заедничко со надреализмот.",
                   "„Композиција (Petite réalité)“, насликана истата година, дава претстава за делата што ги видел Париз: мала реалност што расте кон сонцето."] } },
    { slug: "galaxy-series", date: "2026-01-15", art: 28,
      en: { title: "The galaxy paintings, 1977–1981",
            lead: "How the lake became a universe.",
            body: ["From the late 1970s Naumovski painted a series of galaxies: Lacustrine Galaxy, White Galaxy, Horizontal Galaxy, Galaxy from the Other Side of the Sun and Spiral Galaxy.",
                   "His space is not dark. It is bright and airy, closer to a morning over water than to the night sky, and Lacustrine Galaxy openly joins the lake and the cosmos in its title.",
                   "Spiral Galaxy, from 1981, is one of the last works in the series."] },
      mk: { title: "Сликите со галаксии, 1977–1981",
            lead: "Како езерото стана универзум.",
            body: ["Од крајот на седумдесеттите Наумовски насликал серија галаксии: Езерска галаксија, Бела галаксија, Хоризонтална галаксија, Галаксија од другата страна на сонцето и Спирална галаксија.",
                   "Неговата вселена не е темна. Таа е светла и воздушеста, поблиску до утро над вода отколку до ноќно небо, а Езерска галаксија отворено ги спојува езерото и космосот во својот наслов.",
                   "Спирална галаксија, од 1981 година, е едно од последните дела во серијата."] } },
  ];

  const SERVICES = [
    { key: "licensing", art: 10,
      en: { title: "Reproduction & licensing", text: "Permission to reproduce works from the catalogue in books, articles, films and exhibitions.",
            points: ["High-resolution images on request", "Research and educational use", "Help with credits and captions"] },
      mk: { title: "Репродукција и лиценцирање", text: "Дозвола за репродукција на дела од каталогот во книги, статии, филмови и изложби.",
            points: ["Слики со висока резолуција на барање", "Истражувачка и образовна употреба", "Помош со потписи и наведување"] } },
    { key: "loans", art: 23,
      en: { title: "Exhibition loans", text: "Support for museums and galleries planning an exhibition that includes Naumovski's work.",
            points: ["Locating works and their owners", "Background research and texts", "Curatorial advice"] },
      mk: { title: "Позајмици за изложби", text: "Поддршка за музеи и галерии што планираат изложба со дела од Наумовски.",
            points: ["Пронаоѓање дела и нивни сопственици", "Позадинско истражување и текстови", "Куративни совети"] } },
    { key: "tours", art: 15,
      en: { title: "Guided tours", text: "Guided walks through Naumovski's Ohrid and tours of exhibitions featuring his work.",
            points: ["In English or Macedonian", "Groups of up to 20 people", "School and university groups welcome"] },
      mk: { title: "Водени тури", text: "Водени прошетки низ Охрид на Наумовски и тури низ изложби со негови дела.",
            points: ["На англиски или македонски", "Групи до 20 луѓе", "Добредојдени училишни и студентски групи"] } },
    { key: "workshops", art: 31,
      en: { title: "Educational workshops", text: "Workshops for children and adults on naive art, surrealism and Naumovski's techniques.",
            points: ["For schools and art clubs", "Materials included", "About two hours"] },
      mk: { title: "Едукативни работилници", text: "Работилници за деца и возрасни за наивната уметност, надреализмот и техниките на Наумовски.",
            points: ["За училишта и ликовни клубови", "Материјалите се вклучени", "Околу два часа"] } },
  ];

  // the people behind the catalogue (fictional)
  const TEAM = [
    { email: "elena", en: { name: "Elena Trajkovska", role: "Curator", bio: "Leads the catalogue: researches each painting, its date and its story, and writes the texts." },
                      mk: { name: "Елена Трајковска", role: "Кураторка", bio: "Го води каталогот: ја истражува секоја слика, нејзиниот датум и приказна и ги пишува текстовите." } },
    { email: "marko", en: { name: "Marko Petrovski", role: "Archivist", bio: "Traces works in museum records, auction catalogues and private collections, and checks every source." },
                      mk: { name: "Марко Петровски", role: "Архивист", bio: "Ги бара делата во музејски записи, аукциски каталози и приватни збирки и го проверува секој извор." } },
    { email: "ana",   en: { name: "Ana Stojanovska", role: "Education & tours", bio: "Leads the guided walks through Ohrid and the workshops for schools and art clubs." },
                      mk: { name: "Ана Стојановска", role: "Едукација и тури", bio: "Ги води прошетките низ Охрид и работилниците за училишта и ликовни клубови." } },
    { email: "bojan", en: { name: "Bojan Nikolovski", role: "Licensing & loans", bio: "Handles reproduction requests and helps museums and galleries borrow works for exhibitions." },
                      mk: { name: "Бојан Николовски", role: "Лиценцирање и позајмици", bio: "Ги обработува барањата за репродукција и им помага на музеите и галериите да позајмат дела за изложби." } },
  ];

  const PAGE_TEXT = {
    en: {
      newsIntro: "News, features and stories about Vangel Naumovski and his paintings.",
      teamIntro: "A small team of researchers and educators keeps the catalogue growing and answers your questions.",
      servicesIntro: "We help researchers, publishers, museums and schools work with the paintings of Vangel Naumovski.",
      contactIntro: "Questions, requests or information about a work we have missed? Write to us.",
      cookiesIntro: "This site uses as few cookies as possible.",
      cookiesBody: [
        ["What we use", "One cookie, vn_cookie_consent, remembers whether you accepted or declined cookies. If you accept, a second cookie, vn_lang, remembers your language (English or Macedonian). Both expire after six months."],
        ["What we don't use", "No analytics, advertising or tracking cookies, and no cookies from other companies."],
        ["Editors", "Only people who log in to manage the site receive WordPress login cookies."],
        ["Your choice", "You can change your choice at any time with the button below. Declining does not limit the site in any way; your language simply isn't remembered."],
      ],
      cookieSettings: "Cookie settings",
    },
    mk: {
      newsIntro: "Вести, текстови и приказни за Ванѓел Наумовски и неговите слики.",
      teamIntro: "Мал тим на истражувачи и едукатори се грижи каталогот да расте и одговара на вашите прашања.",
      servicesIntro: "Им помагаме на истражувачи, издавачи, музеи и училишта да работат со сликите на Ванѓел Наумовски.",
      contactIntro: "Прашања, барања или информации за дело што сме го пропуштиле? Пишете ни.",
      cookiesIntro: "Оваа страница користи што е можно помалку колачиња.",
      cookiesBody: [
        ["Што користиме", "Едно колаче, vn_cookie_consent, памети дали сте ги прифатиле или одбиле колачињата. Ако прифатите, второ колаче, vn_lang, го памети вашиот јазик (англиски или македонски). Двете истекуваат по шест месеци."],
        ["Што не користиме", "Без колачиња за аналитика, рекламирање или следење, и без колачиња од други компании."],
        ["Уредници", "Само лицата што се најавуваат за да ја уредуваат страницата добиваат WordPress колачиња за најава."],
        ["Ваш избор", "Изборот можете да го промените во секое време со копчето подолу. Одбивањето на никаков начин не ја ограничува страницата; само вашиот јазик не се памети."],
      ],
      cookieSettings: "Поставки за колачиња",
    },
  };

  /* ============ presentation pages: News, Services, Contact, Cookies ============ */
  const pgMain = document.getElementById("pg-main");
  const pgFooter = document.getElementById("p-footer");
  const PAGES = ["team", "news", "article", "services", "contact", "cookies"];
  const isPage = (name) => PAGES.includes(name);
  const lang2 = () => (locale === "mk" ? "mk" : "en");
  const P = (k) => PAGE_TEXT[lang2()][k];
  const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));
  // dates are spelled out by hand: not every browser ships Macedonian month names
  const MONTHS = {
    en: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    mk: ["јануари", "февруари", "март", "април", "мај", "јуни", "јули", "август", "септември", "октомври", "ноември", "декември"],
  };
  const fmtDate = (iso) => { const [y, m, d] = iso.split("-").map(Number); return `${d} ${MONTHS[lang2()][m - 1]} ${y}`; };
  const BOX = `<svg width="4" height="4" viewBox="0 0 4 4" fill="currentColor" aria-hidden="true"><rect width="4" height="4"/></svg>`;
  // [ LABEL ] with the black wipe-in fill
  const bracket = (label, attrs, tag = "a") =>
    `<${tag} class="group lbl-row uppercase" ${attrs}><span class="lbl-bracket">[</span><span class="lbl lbl-ink"><span>${label}</span></span><span class="lbl-bracket">]</span></${tag}>`;
  const inkLink = (label, href) => `<span class="reveal-block pg-ink"><a class="group" href="${href}"><span class="lbl lbl-ink"><span>${label}</span></span></a></span>`;
  const field = (label, value, i, wide, raw) =>
    `<div class="${wide ? "is-wide" : ""}"><dt>${label}</dt><dd>${raw ? value : `<span class="reveal-block" style="--reveal-delay:${i * 80}ms">${value || "—"}</span>`}</dd></div>`;
  const intro = (title, text) =>
    `<section class="pg-col text-sm"><h1 class="ab-title">${title}</h1><div class="text-paint"><p>${text}</p></div></section>`;

  function renderNews() {
    const L = lang2();
    return intro(t("news"), P("newsIntro")) + `<section class="ab-works pg-list">
      <div class="ab-works-head"><p class="uppercase">${t("articles")}</p></div>
      <div class="ab-rule"></div>
      <ul class="ab-list">${NEWS.map((n) => `<li class="ab-item"><div class="ab-item-bg" aria-hidden="true"></div>
        <a class="ab-row px-global" href="#/news/${n.slug}"><span class="ab-name"><span class="reveal-block">${esc(n[L].title)}</span></span><span class="ab-meta">${fmtDate(n.date)}</span><span class="ab-discover">${t("read")}</span></a>
        <img class="l-thumb" src="${listWorks[n.art - 1].src}" alt="" width="256" height="256" loading="lazy"></li>`).join("")}</ul>
    </section>`;
  }

  function renderArticle(slug) {
    const L = lang2(), n = NEWS.find((x) => x.slug === slug) || NEWS[0], a = n[L], w = listWorks[n.art - 1];
    const next = NEWS[(NEWS.indexOf(n) + 1) % NEWS.length];
    document.title = `${a.title} (${ARTWORKS.length} ${t("titleSuffix")})`;
    return `<article class="pg-art text-sm">
      <div class="pg-art-text">
        <div class="pg-back">${bracket(`← ${t("news")}`, 'href="#/news"')}</div>
        <p class="pg-date">${fmtDate(n.date)}</p>
        <h1 class="pg-title">${esc(a.title)}</h1>
        <p class="pg-lead">${esc(a.lead)}</p>
        <div class="text-paint pg-body">${a.body.map((p) => `<p>${esc(p)}</p>`).join("")}</div>
      </div>
      <figure class="pg-art-fig">
        <a href="#/artwork/${w.id}" aria-label="${esc(w.fullName)}"><div class="reveal-image"><img src="${w.src}" alt="${esc(w.fullName)}"></div></a>
        <figcaption class="aw-muted">${w.id}. ${esc(w.fullName)}</figcaption>
      </figure>
      <dl class="pg-art-sheet aw-sheet"><div class="aw-sheet-grid">
        ${field(t("fDate"), fmtDate(n.date), 0)}
        ${field(t("fSection"), t("news"), 1)}
        ${field(t("relatedWork"), inkLink(esc(w.fullName), `#/artwork/${w.id}`), 2, true, true)}
        ${field(t("nextArticle"), inkLink(esc(next[L].title), `#/news/${next.slug}`), 3, true, true)}
      </div></dl>
    </article>`;
  }

  function renderTeam() {
    const L = lang2(), domain = CONTACT_INFO.email.split("@")[1];
    return intro(t("team"), P("teamIntro")) + `<section class="pg-services pg-team text-sm">${TEAM.map((m, i) => `
      <article class="pg-member">
        <p class="aw-muted">${String(i + 1).padStart(2, "0")}</p>
        <h2 class="pg-svc-title"><span class="reveal-block" style="--reveal-delay:${i * 80}ms">${esc(m[L].name)}</span></h2>
        <p class="aw-muted">${esc(m[L].role)}</p>
        <p>${esc(m[L].bio)}</p>
        ${inkLink(`${m.email}@${domain}`, `mailto:${m.email}@${domain}`)}
      </article>`).join("")}</section>`;
  }

  function renderServices() {
    const L = lang2();
    return intro(t("services"), P("servicesIntro")) + `<section class="pg-services text-sm">${SERVICES.map((s, i) => {
      const w = listWorks[s.art - 1], c = s[L];
      return `<article class="pg-svc">
        <div class="reveal-image"><img src="${w.src}" alt="${esc(w.fullName)}"></div>
        <p class="aw-muted">${String(i + 1).padStart(2, "0")}</p>
        <h2 class="pg-svc-title"><span class="reveal-block" style="--reveal-delay:${i * 80}ms">${esc(c.title)}</span></h2>
        <p>${esc(c.text)}</p>
        <ul>${c.points.map((p) => `<li>${esc(p)}</li>`).join("")}</ul>
        ${bracket(t("enquire"), `href="#/contact" data-subject="${s.key}"`)}
      </article>`;
    }).join("")}</section>`;
  }

  /* contact */
  let pendingSubject = "", contactSent = false;
  const sentBlock = () => `<div class="pg-sent text-sm" id="pg-sent">
      <p class="uppercase"><span class="reveal-block">${t("sentTitle")}</span></p>
      <p>${t("sentText")}</p>
      ${bracket(t("sendAnother"), 'href="#/contact" data-reset')}
    </div>`;
  function renderContact() {
    const L = lang2(), subject = pendingSubject || "general";
    pendingSubject = "";
    const subjects = [["general", t("subjGeneral")], ...SERVICES.map((s) => [s.key, s[L].title])];
    const current = subjects.find(([k]) => k === subject) || subjects[0];
    // subject picker: opens like the Story panel, options slide in like the List filters
    const picker = `<div class="pg-field pg-select" data-f="subject" data-open="false">
        <span class="pg-label" id="pg-subj-label">${t("fSubject")}</span>
        <input type="hidden" name="subject" value="${current[0]}">
        <button type="button" class="pg-sel-btn" aria-haspopup="listbox" aria-expanded="false" aria-labelledby="pg-subj-label pg-subj-value">
          <span class="pg-sel-value" id="pg-subj-value">${esc(current[1])}</span>
          <span class="aw-toggle"><span class="aw-br aw-br-l">[ </span> <span class="pg-sel-minus">-</span><span class="pg-sel-plus">+</span> <span class="aw-br aw-br-r"> ]</span></span>
        </button>
        <div class="pg-sel-list" inert><div class="pg-sel-clip"><div class="pg-sel-opts" role="listbox" aria-labelledby="pg-subj-label">
          ${subjects.map(([k, v], i) => `<button type="button" role="option" class="pg-opt group" style="--i:${i}" data-value="${k}" aria-selected="${k === current[0]}">
            <span class="pg-opt-inner"><span class="check">[${BOX}]</span><span class="lbl lbl-ink"><span>${esc(v)}</span></span></span></button>`).join("")}
        </div></div></div>
        <span class="pg-error"></span>
      </div>`;
    const input = (name, label, attrs = "") =>
      `<label class="pg-field" data-f="${name}"><span class="pg-label">${label}</span>${attrs}<span class="pg-error"></span></label>`;
    const form = `<form class="pg-form text-sm" id="pg-form" novalidate>
        <div class="pg-form-row">
          ${input("name", t("fName"), `<input name="name" autocomplete="name" maxlength="100">`)}
          ${input("email", t("fEmail"), `<input name="email" type="email" autocomplete="email" maxlength="150">`)}
        </div>
        ${picker}
        ${input("message", t("fMessage"), `<textarea name="message" maxlength="5000"></textarea>`)}
        <div class="pg-hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
        <div class="pg-field" data-f="consent">
          <label class="pg-check"><input type="checkbox" name="consent" value="1"><span class="box">[${BOX}]</span><span>${t("consent")}</span></label>
          <span class="pg-error"></span>
        </div>
        <p class="pg-error" id="pg-form-error" role="alert"></p>
        ${bracket(t("send"), 'type="submit"', "button").replace('class="group lbl-row uppercase"', 'class="group lbl-row uppercase pg-submit"')}
      </form>`;
    return intro(t("contact"), P("contactIntro")) + `<section class="pg-contact text-sm">
      <dl class="aw-sheet"><div class="aw-sheet-grid">
        ${field(t("fEmail"), inkLink(CONTACT_INFO.email, `mailto:${CONTACT_INFO.email}`), 0, true, true)}
        ${field(t("fPhone"), inkLink(CONTACT_INFO.phone, `tel:${CONTACT_INFO.phone.replace(/\s/g, "")}`), 1, false, true)}
        ${field(t("fHours"), t("hoursVal"), 2)}
        ${field(t("fAddress"), t("place"), 3, true)}
      </div></dl>
      ${contactSent ? sentBlock() : form}
    </section>`;
  }

  function validate(form) {
    const d = Object.fromEntries(new FormData(form));
    const errs = {};
    const name = (d.name || "").trim(), email = (d.email || "").trim(), msg = (d.message || "").trim();
    if (!name) errs.name = t("errRequired");
    if (!email) errs.email = t("errRequired");
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errs.email = t("errEmail");
    if (!msg) errs.message = t("errRequired");
    else if (msg.length < 10) errs.message = t("errShort");
    if (!d.consent) errs.consent = t("errConsent");
    form.querySelectorAll(".pg-field").forEach((f) => {
      const e = errs[f.dataset.f] || "";
      f.classList.toggle("has-error", !!e);
      f.querySelector(".pg-error").textContent = e;
    });
    return errs;
  }

  async function submitContact(form) {
    const errorLine = form.querySelector("#pg-form-error");
    errorLine.textContent = "";
    const first = Object.keys(validate(form))[0];
    if (first) { form.elements[first]?.focus(); return; }
    const btn = form.querySelector(".pg-submit"), label = btn.querySelector(".lbl span");
    btn.disabled = true;
    label.textContent = t("sending");
    try {
      if (form.elements.website.value) { /* a bot filled in the hidden field: pretend it worked */ }
      else if (window.VN_CONTACT) {
        // WordPress: saved as a private message in the dashboard
        const body = new FormData(form);
        body.append("action", "vn_contact");
        body.append("nonce", window.VN_CONTACT.nonce);
        body.append("lang", locale);
        const res = await fetch(window.VN_CONTACT.url, { method: "POST", body, credentials: "same-origin" });
        const json = await res.json().catch(() => null);
        if (!res.ok || !json?.success) throw new Error("not sent");
      } else {
        await new Promise((r) => setTimeout(r, 700)); // static copy of the site: nothing to send to
      }
      contactSent = true;
      const wrap = document.createElement("div");
      wrap.innerHTML = sentBlock();
      const sent = wrap.firstElementChild;
      gsap.to(form, { autoAlpha: 0, duration: .3, ease: "linear", onComplete: () => {
        form.replaceWith(sent);
        gsap.from(sent, { autoAlpha: 0, duration: .6, ease: "linear" });
        startPageReveals();
        lenis?.resize();
      } });
    } catch {
      errorLine.textContent = t("errSend");
      btn.disabled = false;
      label.textContent = t("send");
    }
  }

  /* cookies */
  const CONSENT = "vn_cookie_consent", LANG_COOKIE = "vn_lang", HALF_YEAR = 180 * 24 * 3600;
  const readCookie = (k) => document.cookie.split("; ").find((c) => c.startsWith(`${k}=`))?.slice(k.length + 1) || "";
  const writeCookie = (k, v, age = HALF_YEAR) => {
    document.cookie = `${k}=${v}; max-age=${age}; path=/; SameSite=Lax${location.protocol === "https:" ? "; Secure" : ""}`;
  };
  const consentLabel = () => ({ accepted: t("cAccepted"), declined: t("cDeclined") })[readCookie(CONSENT)] || t("cNone");
  function renderCookies() {
    return `<section class="pg-col text-sm"><h1 class="ab-title">${t("cookies")}</h1>
      <div class="text-paint"><p>${P("cookiesIntro")}</p>${P("cookiesBody").map(([h, p]) => `<h2>${h}</h2><p>${p}</p>`).join("")}</div>
      <p class="pg-status aw-muted">${t("cookieStatus")}: <span id="pg-consent">${consentLabel()}</span></p>
      <div class="pg-actions">${bracket(P("cookieSettings"), 'type="button" data-cookie-settings', "button")}</div>
    </section>`;
  }

  const banner = document.getElementById("cookie-banner");
  function showBanner() {
    gsap.killTweensOf(banner);
    gsap.fromTo(banner, { autoAlpha: 0, y: 16 }, { autoAlpha: 1, y: 0, duration: .6, ease: "expo.out" });
  }
  function hideBanner() {
    gsap.killTweensOf(banner);
    gsap.to(banner, { autoAlpha: 0, y: 16, duration: .4, ease: "power2.in" });
  }
  function setConsent(v) {
    writeCookie(CONSENT, v);
    if (v === "accepted") writeCookie(LANG_COOKIE, locale); else writeCookie(LANG_COOKIE, "", 0);
    hideBanner();
    const status = document.getElementById("pg-consent");
    if (status) status.textContent = consentLabel();
  }
  // the language is only remembered with consent
  function rememberLang() { if (readCookie(CONSENT) === "accepted") writeCookie(LANG_COOKIE, locale); }
  function restoreLang() {
    const l = readCookie(CONSENT) === "accepted" ? readCookie(LANG_COOKIE) : "";
    if (DICT[l]) locale = l;
  }
  document.getElementById("cb-accept").addEventListener("click", () => setConsent("accepted"));
  document.getElementById("cb-decline").addEventListener("click", () => setConsent("declined"));

  /* rendering, reveals and text painting */
  function renderPage(name, id) {
    pgMain.innerHTML = name === "news" ? renderNews() : name === "article" ? renderArticle(id)
      : name === "team" ? renderTeam() : name === "services" ? renderServices() : name === "contact" ? renderContact() : renderCookies();
    if (name !== "article") document.title = `${t(name)} (${ARTWORKS.length} ${t("titleSuffix")})`;
    pgMain.querySelectorAll(".reveal-image img").forEach(watchLoaded);
    paintPage();
    requestAnimationFrame(() => requestAnimationFrame(startPageReveals));
  }
  // a language switch re-renders the page but keeps what was typed into the form
  function rerenderPage() {
    const form = pgMain.querySelector("#pg-form");
    const kept = form ? [...form.elements].filter((el) => el.name).map((el) => [el.name, el.type === "checkbox" ? el.checked : el.value]) : [];
    if (form) pendingSubject = form.elements.subject.value;
    renderPage(view, routeId);
    const f = pgMain.querySelector("#pg-form");
    if (f) kept.forEach(([k, v]) => { const el = f.elements[k]; if (el) el.type === "checkbox" ? (el.checked = v) : (el.value = v); });
  }

  let pgPending = [];
  const nearView = (r, vh) => r.top < vh * .85 && r.bottom > 0;
  function startPageReveals() {
    pgPending = [];
    pgMain.querySelectorAll(".reveal-block:not(.is-inview)").forEach((el) => pgPending.push({ el, test: anyVisible, onIn: (x) => {
      // list rows come in at random like the List page
      if (x.closest(".ab-list")) x.style.setProperty("--reveal-delay", `${Math.floor(520 * Math.random())}ms`);
      x.classList.add("is-inview");
    } }));
    pgMain.querySelectorAll(".reveal-image:not(.is-inview)").forEach((el) => pgPending.push({ el, test: nearView, onIn: (x) => x.classList.add("is-inview") }));
    checkPageReveals();
  }
  function checkPageReveals() {
    if (!pgPending.length) return;
    const vh = innerHeight;
    pgPending = pgPending.filter((w) => {
      const r = w.el.getBoundingClientRect();
      if (!r.height || !w.test(r, vh)) return true;
      w.onIn(w.el);
      return false;
    });
  }
  // text paints from grey to black as it passes 80% of the screen, as on the About page
  function paintPage() {
    const h = innerHeight;
    pgMain.querySelectorAll(".text-paint").forEach((el) => {
      const r = el.getBoundingClientRect();
      const p = Math.min(1, Math.max(0, (.8 * h - r.top) / (r.height || 1)));
      el.style.setProperty("--paint", `${(p * 100).toFixed(2)}%`);
    });
  }
  window.addEventListener("resize", () => { if (isPage(view)) { paintPage(); checkPageReveals(); } });

  pgMain.addEventListener("click", (e) => {
    if (e.target.closest("[data-cookie-settings]")) { showBanner(); return; }
    const a = e.target.closest('a[href^="#/"]');
    if (!a) return;
    e.preventDefault();
    if (a.hasAttribute("data-reset")) { contactSent = false; rerenderPage(); lenis?.resize(); return; }
    if (a.dataset.subject) pendingSubject = a.dataset.subject;
    navigate(a.getAttribute("href"));
  });
  // subject picker
  function setPicker(sel, open) {
    sel.dataset.open = open;
    sel.querySelector(".pg-sel-btn").setAttribute("aria-expanded", open);
    sel.querySelector(".pg-sel-list").inert = !open;
    lenis?.resize();
  }
  pgMain.addEventListener("click", (e) => {
    const sel = e.target.closest(".pg-select");
    if (!sel) return;
    if (e.target.closest(".pg-sel-btn")) {
      const open = sel.dataset.open !== "true";
      setPicker(sel, open);
      if (open) setTimeout(() => sel.querySelector('.pg-opt[aria-selected="true"]')?.focus({ preventScroll: true }), 50);
      return;
    }
    const opt = e.target.closest(".pg-opt");
    if (!opt) return;
    sel.querySelector('input[name="subject"]').value = opt.dataset.value;
    sel.querySelector(".pg-sel-value").textContent = opt.querySelector(".lbl").textContent;
    sel.querySelectorAll(".pg-opt").forEach((o) => o.setAttribute("aria-selected", o === opt));
    setPicker(sel, false);
    sel.querySelector(".pg-sel-btn").focus({ preventScroll: true });
  });
  pgMain.addEventListener("keydown", (e) => {
    const sel = e.target.closest(".pg-select");
    if (!sel || sel.dataset.open !== "true") return;
    const opts = [...sel.querySelectorAll(".pg-opt")], i = opts.indexOf(document.activeElement);
    if (e.key === "Escape") { e.preventDefault(); setPicker(sel, false); sel.querySelector(".pg-sel-btn").focus(); }
    else if (e.key === "ArrowDown" || e.key === "ArrowUp") {
      e.preventDefault();
      opts[(i + (e.key === "ArrowDown" ? 1 : -1) + opts.length) % opts.length].focus();
    }
  });
  document.addEventListener("click", (e) => {
    const sel = pgMain.querySelector('.pg-select[data-open="true"]');
    if (sel && !sel.contains(e.target)) setPicker(sel, false);
  });

  pgMain.addEventListener("submit", (e) => {
    if (e.target.id !== "pg-form") return;
    e.preventDefault();
    submitContact(e.target);
  });
  pgMain.addEventListener("input", (e) => {
    const f = e.target.closest(".pg-field.has-error");
    if (f) { f.classList.remove("has-error"); f.querySelector(".pg-error").textContent = ""; }
  });

  // page exit: sections fade out one after another, then the footer
  function leavePage(done) {
    const kids = [...pgMain.children];
    const sections = kids.length === 1 ? [...kids[0].children] : kids;
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set([...sections, pgFooter], { clearProps: "opacity,visibility" });
    } });
    tl.to(sections, { autoAlpha: 0, duration: .3, ease: "linear", stagger: { each: .15, from: "start" } }, 0)
      .to(pgFooter, { autoAlpha: 0, duration: .3, ease: "linear" }, .15 * Math.max(0, sections.length - 1));
    return tl;
  }

  /* ============ routing: Grid / List / Gallery / About / Artwork / pages ============ */
  let view = null, routeId = 0, gridBooted = false, lenis = null, backHash = "#/";
  const ROUTES = { "#/": "grid", "#/archive": "list", "#/gallery": "gallery", "#/about": "about",
    "#/team": "team", "#/news": "news", "#/services": "services", "#/contact": "contact", "#/cookies": "cookies" };
  const HREFS = { grid: "#/", list: "#/archive", gallery: "#/gallery", about: "#/about",
    team: "#/team", news: "#/news", article: "#/news", services: "#/services", contact: "#/contact", cookies: "#/cookies" };
  const parseRoute = (hash) => {
    const m = /^#\/artwork\/(\d+)$/.exec(hash);
    if (m) return { name: "artwork", id: Math.min(Math.max(1, +m[1]), listWorks.length) };
    const n = /^#\/news\/([\w-]+)$/.exec(hash);
    if (n) return NEWS.some((x) => x.slug === n[1]) ? { name: "article", id: n[1] } : { name: "news", id: 0 };
    return { name: ROUTES[hash] || "grid", id: 0 };
  };

  function setCurrentNav(name) {
    document.querySelectorAll("#navbar .nav-menu a, #navbar .nav-end a").forEach((a) => {
      a.querySelector(".lbl").classList.toggle("is-current", a.getAttribute("href") === HREFS[name]);
    });
  }

  // coming back to the grid: media slides up into its frame, captions drop in
  function gridReturn() {
    const vis = visibleByDistance();
    if (!vis.length) return;
    const lines = vis.map((e) => e.card.querySelector(".cap-line"));
    const inners = vis.map((e) => e.card.querySelector(".media-inner"));
    const imgs = vis.map((e) => e.card.querySelector(".media-inner img"));
    gsap.set(lines, { yPercent: -100 });
    gsap.set(inners, { yPercent: 100 });
    gsap.set(imgs, { yPercent: -100, scale: 1.15 });
    const tl = gsap.timeline({ onComplete: () => gsap.set([...lines, ...inners, ...imgs], { clearProps: "transform" }) });
    vis.forEach((_, i) => {
      const at = (i / vis.length) * .25;
      tl.to(inners[i], { yPercent: 0, duration: .9, ease: "expo.out" }, at)
        .to(imgs[i], { yPercent: 0, scale: 1, duration: .9, ease: "expo.out" }, at)
        .to(lines[i], { yPercent: 0, duration: .9, ease: "expo.out" }, at);
    });
  }

  const smoothScroll = () => new Lenis({ autoRaf: true, lerp: .1, smoothWheel: true, wheelMultiplier: 1 });

  function applyRoute(name, id = 0) {
    if (name === view && id === routeId) return;
    const from = view;
    view = name;
    routeId = id;
    setCurrentNav(name);
    if (name !== "artwork") backHash = name === "article" ? `#/news/${id}` : HREFS[name];
    // tear down the view being left
    if (from === "list") { listObserver?.disconnect(); document.body.classList.remove("is-list"); }
    if (from === "gallery") { gallery.stop(); document.body.classList.remove("is-gallery"); }
    if (from === "about") document.body.classList.remove("is-about");
    if (from === "artwork") exitArtwork();
    if (isPage(from)) { pgPending = []; document.body.classList.remove("is-page"); document.title = `${ARTWORKS.length} ${t("titleSuffix")}`; }
    lenis?.destroy(); lenis = null;
    window.scrollTo(0, 0);
    if (name !== "grid") {
      if (S.open) closeAll();
      locked = true;
      document.body.classList.add("ui-visible");
    }
    if (name === "list") {
      document.body.classList.add("is-list");
      if (!listBuilt) { renderListCats(); listBuilt = true; }
      renderListBar();
      renderList();
      lenis = smoothScroll();
    } else if (name === "gallery") {
      document.body.classList.add("is-gallery");
      gallery.start();
    } else if (name === "about") {
      document.body.classList.add("is-about");
      enterAbout();
      lenis = smoothScroll();
      lenis.on("scroll", (e) => updateAbout(e.scroll));
    } else if (name === "artwork") {
      enterArtwork(id);
      lenis = smoothScroll();
      lenis.on("scroll", () => { updateArtworkDim(); checkArtworkReveals(); });
    } else if (isPage(name)) {
      document.body.classList.add("is-page");
      if (name === "contact" && from !== "contact") contactSent = false;
      renderPage(name, id);
      lenis = smoothScroll();
      lenis.on("scroll", () => { paintPage(); checkPageReveals(); });
    } else if (!gridBooted) {
      gridBooted = true;
      (document.fonts?.ready ?? Promise.resolve()).then(() => {
        measure();
        requestAnimationFrame(() => requestAnimationFrame(reveal));
      });
    } else {
      locked = false;
      measure();
      if (from) requestAnimationFrame(gridReturn);
    }
  }

  window.addEventListener("hashchange", () => { const r = parseRoute(location.hash); applyRoute(r.name, r.id); });

  /* leaving a view plays its exit animation first, then the route changes */
  const searchBar = document.querySelector(".list-bar");
  const listFooter = document.getElementById("l-footer");
  let leaving = null;

  // grid exit: each visible card's frame slides down while its image counter-slides up
  // (so the picture appears to be wiped away from the top), captions slide up out of view.
  // Cards nearer the centre go first; when a card was clicked, the wave spreads from it
  // and the clicked card itself goes last.
  function leaveGrid(done, clicked = null) {
    locked = true;
    const o = clicked?.getBoundingClientRect();
    const cx = o ? o.left + .5 * o.width : .5 * innerWidth, cy = o ? o.top + .5 * o.height : .5 * innerHeight;
    const cards = [...draggable.querySelectorAll(".gi")].map((el) => {
      const r = el.getBoundingClientRect();
      return {
        r, clicked: el === clicked,
        line: el.querySelector(".cap-line"), inner: el.querySelector(".media-inner"), img: el.querySelector(".media-inner img"),
        dist: Math.hypot(r.left + .5 * r.width - cx, r.top + .5 * r.height - cy),
      };
    }).filter(({ r }) => r.right > -r.width && r.bottom > -r.height && r.left < innerWidth + r.width && r.top < innerHeight + r.height)
      .sort((a, b) => a.dist - b.dist);
    const targets = cards.flatMap((c) => [c.line, c.inner, c.img]);
    gsap.killTweensOf(targets);
    const maxDist = cards.at(-1)?.dist || 1;
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set(targets, { clearProps: "transform" });
      gsap.set(searchUI, { clearProps: "opacity,visibility,transition" });
    } });
    gsap.set(searchUI, { transition: "none" });
    tl.to(searchUI, { autoAlpha: 0, duration: 1, ease: "power2.in" }, 0);
    cards.forEach((c) => {
      const at = c.clicked ? .25 : (c.dist / maxDist) * .15;
      tl.to(c.inner, { yPercent: 100, duration: .45, ease: "power1.out" }, at)
        .to(c.img, { yPercent: -100, scale: 1.15, duration: .45, ease: "power1.out" }, at)
        .to(c.line, { yPercent: -100, duration: .45, ease: "power1.out" }, at);
    });
    return tl;
  }

  // list exit: the list fades out, the filter/search bar fades a little slower
  function leaveList(done) {
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set([listBody, listCount, searchBar, listFooter], { clearProps: "opacity,visibility,transition" });
    } });
    gsap.set(searchBar, { transition: "none" });
    tl.to(searchBar, { autoAlpha: 0, duration: 1, ease: "power2.in" }, 0)
      .to([listCount, listBody], { autoAlpha: 0, duration: .3, ease: "linear", stagger: { each: .15, from: "start" } }, 0)
      .to(listFooter, { autoAlpha: 0, duration: .3, ease: "linear" }, .15);
    return tl;
  }

  // gallery exit: its layers fade out one after another
  function leaveGallery(done) {
    const parts = [galleryWrap, galleryLink, galleryLabel];
    const tl = gsap.timeline({ onComplete: () => {
      done();
      gsap.set([galleryWrap, galleryLink], { clearProps: "opacity,visibility" });
      gsap.set(galleryLabel, { autoAlpha: 0 });
    } });
    tl.to(parts, { autoAlpha: 0, duration: .3, ease: "linear", stagger: { each: .15, from: "start" } }, 0);
    return tl;
  }

  function navigate(hash, clicked = null) {
    const target = parseRoute(hash);
    if ((target.name === view && target.id === routeId) || leaving) return;
    // switch right away (the hashchange that follows is then a no-op), so nothing flashes back
    const done = () => { leaving = null; location.hash = hash; applyRoute(target.name, target.id); };
    leaving = view === "grid" ? leaveGrid(done, clicked) : view === "list" ? leaveList(done)
      : view === "about" ? leaveAbout(done) : view === "artwork" ? leaveArtwork(done) : isPage(view) ? leavePage(done) : leaveGallery(done);
  }
  document.querySelectorAll(Object.keys(ROUTES).map((h) => `a[href="${h}"]`).join(", ")).forEach((a) => {
    if (a.closest("#aw-artist-reveal")) return; // handled with the artwork page
    a.addEventListener("click", (e) => { e.preventDefault(); navigate(a.getAttribute("href")); });
  });
  document.querySelector(".aw-close").addEventListener("click", (e) => { e.preventDefault(); navigate(backHash); });

  /* ============ boot ============ */
  restoreLang();
  applyLocale();
  if (!readCookie(CONSENT)) setTimeout(showBanner, 1500);
  { const r = parseRoute(location.hash); applyRoute(r.name, r.id); }
})();
</script>
<?php wp_footer(); ?>
</body>
</html>
