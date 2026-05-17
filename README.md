# MultiAnalityca

Sitio web WordPress de MultiAnalityca (clínica médica).

## Stack

- **CMS**: WordPress
- **Tema**: Avada + Avada-Child-Theme
- **Base de datos**: MySQL/MariaDB (`multiana_clinica77`, prefijo `wptt_`)
- **Servidor**: Apache (Laragon en desarrollo local)
- **PHP**: 7.2.24+ (recomendado 8.3)

## Desarrollo local

Requiere Laragon con Apache + MySQL/MariaDB y `mod_rewrite` habilitado.

```bash
# Listar plugins
wp plugin list

# Exportar base de datos
wp db export

# Limpiar caché
wp cache flush
```

Ver `CLAUDE.md` para documentación completa del proyecto.
