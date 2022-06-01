<template>
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form v-on:submit.prevent="changePasswordModal()">
                    <div class="modal-body">
                        <label for="email">Password</label>
                        <div class="form-group">
                            <input v-model="form.password" type="password" class="form-control" id="password" name="password">
                            <span v-if="errors.password.invalid" class="message-error">{{errors.password.message}}</span>
                        </div>
                        <label for="email">Confirm password</label>
                        <div class="form-group">
                            <input v-model="form.password_confirmation" type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            <span v-if="errors.password_confirmation.invalid" class="message-error">{{errors.password_confirmation.message}}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close-change-password-modal" data-dismiss="modal">Close</button> 
                        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> <span class="d-none d-sm-inline">Save</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import UserServices from '../services/UserServices.js';

    export default {
        props: ['type','user_id'],
        mixins: [UserServices],
        data() {
            return {
                form: {
                    password: '',
                    password_confirmation: '',
                },
                errors: {
                    password: {
                        invalid: false,
                        message: ""
                    },
                    password_confirmation: {
                        invalid: false,
                        message: ""
                    }
                }
            }
        },

        methods: {
            changePasswordModal() {
                var hasError = false;

                if (!this.form.password) {
                    this.errors.password.invalid = true;
                    this.errors.password.message = "Password is required";
                    hasError = true;
                }else{
                    this.errors.password.invalid = false;
                    this.errors.password.message = "";
                }
                if (!this.form.password_confirmation) {
                    this.errors.password_confirmation.invalid = true;
                    this.errors.password_confirmation.message = "Password confirmation is required";
                    hasError = true;
                }else{
                    this.errors.password_confirmation.invalid = false;
                    this.errors.password_confirmation.message = "";
                }

                if(!hasError && this.form.password_confirmation != this.form.password){
                    this.errors.password_confirmation.invalid = true;
                    this.errors.password_confirmation.message = "The password confirmation does not match";
                    hasError = true;
                }

                if(!hasError){
                    let data = {
                        'password': this.form.password,
                        'password_confirmation': this.form.password_confirmation
                    }
                    Helpers.block();
                    if(this.type == 'user'){
                        this.changeUserPassword(this.user_id, data, this.changeUserPasswordCallback);
                    }else{
                        this.changeProfilePassword(data, this.changeUserPasswordCallback);
                    }
                }
            },
            changeUserPasswordCallback(response) {
                if (response.status == 1) {
                    this.form.password = "";
                    this.form.password_confirmation = "";
                    for (const key in this.errors) {
                        this.errors[key].message = '';
                        this.errors[key].invalid = true;
                    }
                    $('#changePasswordModal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    toastr.success(response.message, 'Success');
                } else {
                    if (Helpers.isAssoc(response.errors)) {
                        let errors = [];
                        for (var i in response.errors) {
                            if(this.errors.hasOwnProperty(i)){
                                this.errors[i].invalid = true;
                                this.errors[i].message = response.errors[i][0];
                            }
                            errors.push('<span>' + response.errors[i] + '</span></br>')
                        }
                        toastr.error(errors, 'Some error(s) has occurred');
                    } else {
                        toastr.error(response.errors[0], 'An error has occurred');
                    }
                }
                Helpers.unblock();
            },
        }
    }
</script>
<style>
    .input-error-select {
        color: #d61212 !important;
        border: 1px solid #b60707 !important;
        border-radius: 5px
    }

    .message-error {
        color: #d61212;
        float: right;
        padding-top: 10px;
        font-size: 12px;
    }

    label {
        font-size: 12px
    }

    .select2-selection__rendered {
        font-size: 12px !important;
    }

    .vcenter {
        display: inline-block;
        vertical-align: middle;
        float: none;
    }
</style>
<style scoped>
    input,
    select,
    textarea {
        font-size: 12px;
    }

    .logo-list {
        width: 80px;
        height: auto;
    }
</style>