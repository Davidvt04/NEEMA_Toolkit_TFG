<?php
/**
 * Plantilla de correo electrónico para notificar el rechazo de un recurso
 * 
 * Variables disponibles:
 * @var string $autor_nombre - Nombre del autor del recurso
 * @var string $recurso_titulo - Título del recurso rechazado
 * @var string $motivo_rechazo - Motivo del rechazo
 * @var string $recurso_editar_url - URL para editar el recurso
 * @var string $fecha_rechazo - Fecha de rechazo
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recurso Rechazado - NEEMA Toolkit</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Open Sans', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 560px; padding: 20px; background: #3D3073; border-radius: 10px; margin: 40px auto; font-size: 15px; color: #ffffff;">
        
        <!-- Header -->
        <div style="text-align: center; font-weight: bold; font-size: 28px; padding: 15px 0; border-bottom: 3px solid #5A45A0;">
            NEEMA Toolkit
        </div>
        
        <!-- Body -->
        <div style="padding: 30px; line-height: 1.6;">
            <h2 style="font-size: 22px; font-weight: 600; text-align: center; margin-bottom: 25px;">
                Vaya... tu recurso <?php echo esc_html($recurso_titulo); ?> requiere cambios
            </h2>
            
            <p style="text-align: center; font-size: 16px; margin-bottom: 10px;">
                Hola <strong><?php echo esc_html($autor_nombre); ?></strong>,
            </p>
            
            <p style="text-align: center; font-size: 16px; margin-bottom: 30px;">
                Tu recurso ha sido revisado por el equipo de administración de NEEMA Toolkit y requiere cambios antes de ser publicado.
            </p>
            
            <!-- Cuadro destacado con información del recurso -->
            <div style="background-color: rgba(246, 235, 248, 0.15); border-left: 4px solid #f6ebf8; border-radius: 4px; padding: 20px; margin: 25px 0;">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #e0dff4; text-transform: uppercase; letter-spacing: 0.5px;">
                    Recurso que Requiere Cambios
                </p>
                <p style="margin: 0 0 15px 0; font-size: 20px; font-weight: bold; color: #ffffff;">
                    <?php echo esc_html($recurso_titulo); ?>
                </p>
                <p style="margin: 0; font-size: 14px; color: #e0dff4;">
                    <strong>Fecha de revisión:</strong> <?php echo esc_html($fecha_rechazo); ?>
                </p>
            </div>
            
            <!-- Motivo del rechazo -->
            <div style="background-color: rgba(255, 159, 28, 0.15); border-left: 4px solid #ff9f1c; border-radius: 4px; padding: 20px; margin: 25px 0;">
                <p style="margin: 0 0 10px 0; font-size: 14px; color: #ff9f1c; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold;">
                    Motivo del Rechazo
                </p>
                <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #ffffff;">
                    <?php echo nl2br(esc_html($motivo_rechazo)); ?>
                </p>
            </div>
            
            <p style="text-align: center; font-size: 16px; margin: 25px 0 30px 0;">
                Por favor, revisa el motivo del rechazo y realiza las correcciones necesarias. Una vez que hayas actualizado el recurso, podrás volver a enviarlo para revisión.
            </p>
            
            <!-- Botón de acción -->
            <div style="text-align: center; margin-bottom: 30px;">
                <a style="background-color: #f6ebf8; color: #3d3073; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block;" href="<?php echo esc_url($recurso_editar_url); ?>">
                    Editar Recurso
                </a>
            </div>
            
            <p style="text-align: center; font-size: 16px; margin-top: 25px;">
                Muchas gracias por tu colaboración.
            </p>
        </div>
        
        <!-- Footer -->
        <div style="text-align: center; font-size: 13px; color: #d1cfe8; padding: 20px 30px;">
            Este es un mensaje automático de NEEMA Toolkit.<br />
            Si tienes alguna pregunta sobre el rechazo, no dudes en contactar con el equipo de administración.
        </div>
        
    </div>
</body>
</html>
