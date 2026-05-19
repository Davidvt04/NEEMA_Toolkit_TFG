<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalHTML = `
        <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.7); z-index: 999999; justify-content: center; align-items: center; padding: 20px;">
            <div style="position: relative; background: white; padding: 40px; border-radius: 15px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); border-top: 5px solid #d63638; text-align: center;">
                <div style="width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; background-color: #fce4e4; color: #d63638;">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </div>
                <h3 style="color: #3D3073; font-size: 1.8rem; margin-bottom: 15px; font-weight: 700;"><?php pll_e('¿Estás seguro?'); ?></h3>
                <p style="color: #555; font-size: 1rem; line-height: 1.6; margin-bottom: 25px;"><?php pll_e('Esta acción eliminará permanentemente tu cuenta y todos tus datos. Esta acción no se puede deshacer.'); ?></p>
                
                <form method="post" id="deleteForm" style="display: flex; flex-direction: column; gap: 15px;">
                    <input type="hidden" name="delete_account" value="1">
                    
                    <div style="display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap;">
                        <button type="button" class="btn-cancel" onclick="closeDeleteModal()" style="flex: 1; background-color: #6c757d; color: white; border: 2px solid #6c757d; padding: 12px 20px; border-radius: 40px; cursor: pointer; font-weight: 600; font-size: 1rem; min-width: 120px; transition: all 0.3s ease;"><?php pll_e('Cancelar'); ?></button>
                        <button type="submit" class="btn-delete-submit" style="flex: 1; background-color: #d63638; color: white; border: 2px solid #d63638; padding: 12px 20px; border-radius: 40px; cursor: pointer; font-weight: 600; font-size: 1rem; min-width: 120px; transition: all 0.3s ease;"><?php pll_e('Eliminar cuenta'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    }
});

function openDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        document.body.style.overflow = 'hidden';
    }
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('mouseover', function(e) {
        if (e.target.classList.contains('btn-cancel')) {
            e.target.style.backgroundColor = '#5a6268';
            e.target.style.transform = 'translateY(-2px)';
            e.target.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        }
        if (e.target.classList.contains('btn-delete-submit')) {
            e.target.style.backgroundColor = '#b32d2e';
            e.target.style.transform = 'translateY(-2px)';
            e.target.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.2)';
        }
    });
    
    document.addEventListener('mouseout', function(e) {
        if (e.target.classList.contains('btn-cancel')) {
            e.target.style.backgroundColor = '#6c757d';
            e.target.style.transform = 'translateY(0)';
            e.target.style.boxShadow = 'none';
        }
        if (e.target.classList.contains('btn-delete-submit')) {
            e.target.style.backgroundColor = '#d63638';
            e.target.style.transform = 'translateY(0)';
            e.target.style.boxShadow = 'none';
        }
    });
});
</script>
