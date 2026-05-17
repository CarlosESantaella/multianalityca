# Guía de Configuración SEO - MultiAnalityca

Esta guía detalla los pasos para completar la configuración SEO del sitio en el panel de WordPress.

---

## 1. Activar IndexNow (Indexación Rápida)

IndexNow notifica automáticamente a Bing y Yandex cuando publicas o actualizas contenido.

### Pasos:
1. Ve a **WordPress Admin > Yoast SEO > Settings**
2. Selecciona **Site features** en el menú lateral
3. Busca la opción **IndexNow**
4. Activa el interruptor

✅ **Resultado:** Los buscadores recibirán notificación inmediata de cambios en tu contenido.

---

## 2. Verificación de Google Search Console

Ya tienes el archivo de verificación (`google204d9764c09dbc8f.html`). Para completar la configuración:

### Pasos:
1. Ve a [Google Search Console](https://search.google.com/search-console)
2. Haz clic en **Agregar propiedad**
3. Selecciona **Dominio** o **Prefijo de URL**
4. Para Prefijo de URL: `https://multianalityca.com`
5. Verifica con el archivo HTML existente

### Enviar Sitemap:
1. En Search Console, ve a **Sitemaps**
2. Agrega: `https://multianalityca.com/sitemap_index.xml`
3. Haz clic en **Enviar**

---

## 3. Verificación de Bing Webmaster Tools

### Pasos:
1. Ve a [Bing Webmaster Tools](https://www.bing.com/webmasters)
2. Inicia sesión con cuenta Microsoft
3. Agrega tu sitio: `https://multianalityca.com`
4. Selecciona verificación con **Meta tag**
5. Copia el contenido del atributo `content` (ej: `1234567890ABCDEF`)
6. En WordPress: **Yoast SEO > Settings > Webmaster tools**
7. Pega el código en **Bing verification code**
8. Guarda cambios
9. Vuelve a Bing y haz clic en **Verificar**

### Enviar Sitemap en Bing:
1. En Bing Webmaster, ve a **Sitemaps**
2. Envía: `https://multianalityca.com/sitemap_index.xml`

---

## 4. Verificación de Yandex Webmaster (Opcional)

Útil si tienes tráfico de países de habla rusa.

### Pasos:
1. Ve a [Yandex Webmaster](https://webmaster.yandex.com)
2. Agrega sitio y obtén código de verificación
3. En WordPress: **Yoast SEO > Settings > Webmaster tools**
4. Pega en **Yandex verification code**

---

## 5. Configuración de Crawl Optimization

Optimiza cómo los bots rastrean tu sitio.

### Pasos:
1. Ve a **Yoast SEO > Settings > Crawl optimization**
2. Activa las siguientes opciones:

| Opción | Recomendación |
|--------|---------------|
| Remove generator tag | ✅ Activar |
| Remove WLW manifest link | ✅ Activar |
| Remove RSD/WLW links | ✅ Activar |
| Remove shortlinks | ✅ Activar |
| Remove REST API links | ✅ Activar |
| Remove oEmbed links | ✅ Activar |
| Remove global feed | ❌ Mantener desactivado |

3. En **Bot blocking**:
   - ✅ Block GPTBot (OpenAI)
   - ✅ Block CCBot (Common Crawl/AI)
   - ✅ Block Google-Extended (Bard)

---

## 6. Configuración de Schema (Organization)

El Schema ya está implementado en código. Para completar en Yoast:

### Pasos:
1. Ve a **Yoast SEO > Settings > Site basics**
2. En **Site representation**:
   - Tipo: **Organization**
   - Nombre: `MultiAnalityca S.A.`
   - Logo: Sube el logo del laboratorio
3. En **Social profiles** agrega:
   - Facebook: URL de la página
   - Instagram: URL del perfil

---

## 7. Configuración de Content Types

### Páginas:
1. Ve a **Yoast SEO > Settings > Content types > Pages**
2. Configuración:
   - Show pages in search results: ✅ Yes
   - SEO title template: `%%title%% | MultiAnalityca`
   - Meta description template: (dejar vacío para usar personalizado)

### Posts:
1. Ve a **Content types > Posts**
2. Configuración:
   - Show posts in search results: ✅ Yes
   - SEO title template: `%%title%% | Blog MultiAnalityca`
   - Meta description template: (dejar vacío)

---

## 8. Configuración de Taxonomías

### Categorías:
1. Ve a **Yoast SEO > Settings > Categories & Tags**
2. Categorías:
   - Show in search results: ✅ Yes (si tienen contenido único)
3. Tags:
   - Show in search results: ❌ No (evita contenido duplicado)

---

## 9. Configuración de Archives

### Pasos:
1. Ve a **Yoast SEO > Settings > Advanced > Archives**
2. Configuración:
   - Author archives: ❌ Disabled (si solo hay 1 autor)
   - Date archives: ❌ Disabled (evita duplicados)

---

## 10. Breadcrumbs (Migas de Pan)

### Pasos:
1. Ve a **Yoast SEO > Settings > Site basics > Breadcrumbs**
2. Activa **Enable breadcrumbs**
3. Configuración:
   - Separator: `»` o `>`
   - Home text: `Inicio`
   - Prefix: (dejar vacío)

> **Nota:** Los breadcrumbs Schema ya están implementados en el código.

---

## 11. Verificación Final

### Checklist:
- [ ] IndexNow activado
- [ ] Google Search Console verificado
- [ ] Sitemap enviado a Google
- [ ] Bing Webmaster Tools verificado
- [ ] Sitemap enviado a Bing
- [ ] Crawl optimization configurado
- [ ] Schema Organization completado
- [ ] Content types configurados
- [ ] Archives deshabilitados
- [ ] Breadcrumbs activados

### Herramientas de Validación:
- **Schema:** [Google Rich Results Test](https://search.google.com/test/rich-results)
- **Meta tags:** [SEO Site Checkup](https://seositecheckup.com/)
- **Sitemap:** `https://multianalityca.com/sitemap_index.xml`

---

## 12. Mantenimiento SEO

### Semanal:
- Revisar Google Search Console para errores de cobertura
- Monitorear posiciones con Yoast (integración Wincher)

### Mensual:
- Revisar Core Web Vitals
- Actualizar contenido de páginas principales
- Crear nuevos posts de blog con keywords relevantes

### Trimestral:
- Auditoría SEO completa con Yoast
- Revisar y actualizar meta descripciones
- Analizar keywords con mejor rendimiento

---

## Datos de Contacto del Negocio

Estos datos están configurados en el Schema:

| Campo | Valor |
|-------|-------|
| Nombre | MultiAnalityca S.A. |
| Teléfono | +593 95 885 0928 |
| Dirección | Jorge Erazo N50-109 y Cristóbal Sandoval |
| Ciudad | Quito, Pichincha |
| País | Ecuador |
| Horario | Lunes a Viernes 8:00 - 17:00 |

---

## Archivos Modificados

| Archivo | Descripción |
|---------|-------------|
| `wp-content/themes/Avada-Child-Theme/functions.php` | Schema MedicalBusiness, Service, Breadcrumbs |
| `wptt_postmeta` (BD) | Títulos y descripciones SEO de 17 páginas |

---

*Última actualización: Enero 2026*
*Implementado por: Claude Code*
