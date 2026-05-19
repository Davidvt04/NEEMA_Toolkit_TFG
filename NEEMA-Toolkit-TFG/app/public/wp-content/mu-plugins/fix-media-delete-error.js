/**
 * Fix Media Library Delete Error
 * 
 * This script fixes the "Error al borrar el adjunto" message that appears
 * when deleting media files from the library, even though the file is
 * successfully deleted (200 OK response).
 * 
 * The issue is caused by the aria-hidden attribute on the #wpwrap element
 * while a descendant element still has focus, which blocks the focus management
 * and triggers the error callback even on successful deletion.
 */

(function($) {
	'use strict';
	
	// Wait for the media views to be loaded
	$(document).ready(function() {
		// Check if wp.media.view.AttachmentCompat exists
		if ( typeof wp !== 'undefined' && 
		     typeof wp.media !== 'undefined' && 
		     typeof wp.media.view !== 'undefined' && 
		     typeof wp.media.view.Attachment !== 'undefined' &&
		     typeof wp.media.view.Attachment.Details !== 'undefined' ) {
			
			// Override the deleteAttachment method
			var originalDeleteAttachment = wp.media.view.Attachment.Details.prototype.deleteAttachment;
			
			wp.media.view.Attachment.Details.prototype.deleteAttachment = function( event ) {
				event.preventDefault();
				
				var self = this;
				var l10n = wp.media.view.l10n;
				
				this.getFocusableElements();
				
				if ( window.confirm( l10n.warnDelete ) ) {
					// Move focus BEFORE destroying to avoid aria-hidden conflict
					this.moveFocus();
					
					// Small delay to ensure focus has moved
					setTimeout(function() {
						self.model.destroy({
							wait: true,
							success: function() {
								
								// Close the modal if it's open
								if ( self.controller && self.controller.close ) {
									self.controller.close();
								}
								
								// Redirect to the media library page
								setTimeout(function() {
									window.location.href = '/wp-admin/upload.php';
								}, 100);
							},
							error: function( model, response ) {
								// Only show error if it's a real error (not 200 OK)
								if ( response && response.status && response.status !== 200 ) {
									window.alert( l10n.errorDeleting );
								} else {
									
									// Close the modal if it's open
									if ( self.controller && self.controller.close ) {
										self.controller.close();
									}
									
									// Redirect to the media library page
									setTimeout(function() {
										window.location.href = '/wp-admin/upload.php';
									}, 100);
								}
							}
						});
					}, 50);
				}
			};
		}
	});
	
})(jQuery);
