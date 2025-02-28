<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit custom message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="edit-form">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Key input --}}
                    <div class="form-group">
                        <label for="key-edit" class="input-label">Key <span class="text-danger">*</span></label>
                        <input type="text" name="key" id="key-edit" class="form-control" required placeholder="Key">
                    </div>
                    {{-- End of key input --}}

                    {{-- Value input --}}
                    <div class="form-group">
                        <label for="value-edit" class="input-label">Value <span class="text-danger">*</span></label>
                        <textarea name="value" id="value-edit" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                    {{-- End of value input --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
