<template>
    <div>
        <div class="card">
            <div class="card-header">
                <div class="float-left">
                    <h4 class="card-title">Create User</h4>
                </div>
                <div class="float-right">
                    <ul class="list-inline mb-0" style="margin-top:-5px;">
                        <li><a href="#" @click="showPage(pagination.current_page)" style="padding: 0 8px;"><i class="ft-rotate-cw"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form class="form" v-on:submit.prevent="create()">
                        <h5 class="form-section"><i class="ft-info"></i>Info</h5>
                        <div class="row">
                            <div class="col-image">
                                <fieldset class="form-group">
                                    <div class="avatar-wrapper">
                                        <img class="profile-pic-admin" src="" />
                                        <div class="upload-button-admin">
                                            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                        </div>
                                        <input class="file-upload-admin" type="file" accept="image/*" ref="picture" />
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-data">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="firstname">Firstname</label>
                                            <input v-model="form.firstname" type="text" class="form-control" id="firstname" name="firstname">
                                            <span v-if="errors.firstname.invalid" class="message-error">{{errors.firstname.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="lastname">Lastname</label>
                                        <input v-model="form.lastname" type="text" class="form-control" id="lastname" name="lastname">
                                        <span v-if="errors.lastname.invalid" class="message-error">{{errors.lastname.message}}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input v-model="form.email" type="text" class="form-control" id="email" name="email">
                                            <span v-if="errors.email.invalid" class="message-error">{{errors.email.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input v-model="form.phone" type="text" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-8">
                                        <div class="form-group">
                                            <label for="status">Affiliate</label>
                                            <select id="select2-users-on-details" class="select2-placeholder form-control" style="width: 100%"></select>
                                            <span v-if="errors.affiliate_id.invalid" class="error_message">{{errors.affiliate_id.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="city">Percent</label>
                                            <input v-model="form.percent" type="text" class="form-control" id="percent">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions float-right" style="border-top: none;">
                            <button type="button" class="btn btn-outline-blue-grey"  @click="$router.push({ name: 'users_list'})"><i class="fa fa-chevron-left"></i> <span class="d-none d-sm-inline">Back</span></button> 
                            <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> <span class="d-none d-sm-inline">Create</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import UserServices from '../services/UserServices.js';
    export default {
        mixins: [UserServices],
        data() {
            return {
                form: {
                    firstname: '',
                    lastname: '',
                    email: '',
                    phone: '',
                    status: 'active',
                    affiliate_id: 0,
                    percent: 0,
                    avatar:null
                },
                errors: {
                    firstname: {
                        invalid: false,
                        message: ""
                    },
                    lastname: {
                        invalid: false,
                        message: ""
                    },
                    email: {
                        invalid: false,
                        message: ""
                    },
                    affiliate_id: {
                        invalid: false,
                        message: ""
                    },
                    percent: {
                        invalid: false,
                        message: ""
                    }
                }
                
            }
        },

        methods: {
            create() {
                var hasError = false;
                var required = {
                    firstname:['string'],
                    lastname:['string'],
                    email:['string','email'],
                    phone:['string']
                }

                for (const key in this.form) {
                    if (this.errors.hasOwnProperty(key)) {
                        this.errors[key].invalid = false;
                        this.errors[key].message = '';
                    }
                    if (required.hasOwnProperty(key)) {
                        for (const filter in required[key]) {
                            if(filter == 'string'){
                                this.form[key] = (this.form[key] + '').trim();
                                if(this.form[key] != ''){
                                    this.errors[key].invalid = true;
                                    this.errors[key].message = 'This field is required';
                                    hasError = true;
                                    break;
                                }
                            }
                            if(filter == 'email'){
                                this.form[key] = (this.form[key] + '').trim();
                                if(!Helpers.validateEmail(this.form[key])){
                                    this.errors[key].invalid = true;
                                    this.errors[key].message = "The email isn't valid";
                                    hasError = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                if(!hasError){
                    Helpers.block();
                    this.createUser(Helpers.jsonToFormData(this.form), this.createUserCallback);
                }
            },
            createUserCallback(response) {
                if (response.status == 1) {
                    toastr.success(response.message, 'Success');
                    this.$router.push({ name: 'user_details', params: { user_id: response.data.id }})
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
            readURL(input) {
                if (input.files && input.files[0]) {
                    this.form.avatar = input.files[0];
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.profile-pic-admin').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        },
        mounted() {
            var self = this;
            this.$nextTick(function () {
                $("#select2-users-on-create").select2({
                    placeholder: 'Select affiliate',
                    ajax: {
                        url: '/api/admin/users/search',
                        dataType: 'json',
                        delay: 250,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: function (params) {
                            params.term = (typeof params.term == 'undefined') ? '' : params.term;
                            var terms = {
                                query: '+*' + params.term.trim().replace(" ", "* +*") + '*', // search term
                                fields: ['id', 'firstname','lastname'],
                                page: params.page,
                                limit: 5
                            } 
                            return terms;
                        },
                        processResults: function (response, params) {
                            params.page = params.page || 1;
                            return {
                                results: response.data,
                                pagination: {
                                    more: (params.page) < response.pagination.total_pages
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) { return markup; }, 
                    templateResult: function (repo) {
                        if (repo.loading) return repo.text;
                        var markup = "<div class='select2-result-repository clearfix'>" + repo.firstname +' '+ repo.lastname + "</div>";
                        return markup;
                    }, 
                    templateSelection: function (repo) {
                        if (repo.firstname) {
                            self.form.affiliate_id = repo.id;
                            return repo.firstname + ' '+ repo.lastname;
                        } else {
                            return repo.text;
                        }
                    }
                });
                $(".file-upload-admin").on('change', function () {
                    self.readURL(this);
                });
                $(".upload-button-admin").on('click', function () {
                    $(".file-upload-admin").click();
                });
                $(".nav-item").removeClass("active");
                $('#users').addClass('active')
            });
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

    .avatar-wrapper {
        margin: 0px auto;
        position: relative;
        height: 150px;
        width: 150px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 1px 1px 15px -5px black;
        transition: all .3s ease;
    }

    .avatar-wrapper:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .avatar-wrapper:hover .profile-pic-admin {
        opacity: .5;
    }

    .avatar-wrapper .profile-pic-admin {
        height: 100%;
        width: 100%;
        transition: all .3s ease;
    }

    .avatar-wrapper .profile-pic-admin:after {
        font-family: FontAwesome;
        content: "\f007";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        font-size: 150px;
        background: #ecf0f1;
        color: #34495e;
        text-align: center;
    }

    .avatar-wrapper .upload-button-admin {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }

    .avatar-wrapper .upload-button-admin .fa-arrow-circle-up {
        position: absolute;
        font-size: 179px;
        top: -15px;
        left: 2;
        text-align: center;
        opacity: 0;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
        color: #34495e;
    }

    .avatar-wrapper .upload-button-admin:hover .fa-arrow-circle-up {
        opacity: .9;
    }

    .col-image{
        padding: 0px 15px;
        width: 160px;
    }
    .col-data{
        padding: 0px 15px;
        width: calc(100% - 160px);
    }

    @media (max-width: 576px) {
        .col-image{
            padding: 0px 15px;
            margin: 0px auto;
            width: 100%;
        }
        .col-data{
            padding: 0px 15px;
            width: 100%;
        }   
    }
</style>