<template>
    <div>
        <div class="card">
            <div class="card-header">
                <div class="float-left">
                    <h4 class="card-title">My Profile</h4>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body" style="padding-top:0px;">
                    <ul class="nav nav-tabs mt-1">
                        <li class="nav-item">
                            <a class="nav-link active" id="base-general" data-toggle="tab" aria-controls="tab1"
                                href="#general" aria-expanded="true"><i class="fa fa-home"></i>General</a>
                        </li>
                    </ul>
                     <div class="tab-content px-1 pt-1">
                        <div role="tabpanel" class="tab-pane active" id="general" aria-expanded="true" aria-labelledby="base-general">
                            <form class="form" v-on:submit.prevent="update()">
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
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input v-model="form.email" type="text" class="form-control" id="email" name="email">
                                                    <span v-if="errors.email.invalid" class="message-error">{{errors.email.message}}</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="form-group">
                                                    <label for="phone">Phone</label>
                                                    <input v-model="form.phone" type="text" class="form-control" id="phone" name="phone">
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-2">
                                                <div class="form-group">
                                                    <button class="btn btn-outline-blue" style="margin-top:26px;" type="button" data-toggle="modal" data-target="#changePasswordModal"><i class="fa fa-key"></i> New Password</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions float-right" style="border-top: none;">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> <span class="d-none d-sm-inline">Save</span></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <password-reset-modal type="profile"></password-reset-modal>
    </div>
</template>

<script>
    import UserServices from './../services/UserServices.js';
    import UserResetPasswordModalComponent from './UserResetPasswordModalComponent';
    export default {
        mixins: [UserServices],
        components:{
            "password-reset-modal":UserResetPasswordModalComponent
        },
        data() {
            return {
                form: {
                    firstname: '',
                    lastname: '',
                    email: '',
                    phone: '',
                    avatar: null
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
                    }
                }
            }
        },

        methods: {
            update(){
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
                    this.updateProfile(Helpers.jsonToFormData(this.form), this.updateProfileCallback);
                }
            },
            updateProfileCallback(response){
                if(response.status > 0){
                    toastr.success(response.message, 'Success');
                }else{
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
            getLedgers(page){
                Helpers.block();
                var params = {
                    page: (typeof page == 'undefined') ? this.paginator.current_page : page,
                    limit: this.offset,
                    fields: ['id','user_id', 'type', 'debit','credit', 'sbalance', 'ebalance','status','created_at'],
                    orderby:{
                        id: 'DESC'
                    },
                    with:['user'],
                    where:[
                        ['user_id', this.user_id]
                    ]
                };
                this.getLedgersCall(params, this.getLedgersCallback);
            },
            getLedgersCallback(response){
                console.log(response);
                if(response.status > 0){
                    if(response.data.length > 0){
                        this.paginator = response.pagination;
                    }else{
                        this.paginator= {
                            total: 0,
                            per_page: 2,
                            from: 1,
                            to: 0,
                            current_page: 1
                        };
                    }
                    this.current_ledgers= response.data;
                }else{
                    swal("Error!", response.errors[0], "error");
                }
                 Helpers.unblock();
            },
            getProfileCallback(response){
                if(response.status > 0){
                    for(let i in this.form){
                        if(i == 'avatar' && response.data[i]){
                            console.log(response.data[i], Helpers.getEnv('MIX_CDN_URL', ''),process.env['MIX_CDN_URL']);
                            $('.profile-pic-admin').attr('src', Helpers.getEnv('MIX_CDN_URL', '') + '/' + response.data[i]);
                        }else{
                            this.form[i] = response.data[i];
                        }
                    }
                }else{
                    swal({
                        title:'Error!', 
                        text: response.errors[0], 
                        icon: 'error'
                    }).then((result) => {
                        
                    });
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
        created() {
            Helpers.block();
            var params = {
                fields:['firstname', 'lastname', 'phone', 'email', 'avatar'],
            }
            this.getProfile(params, this.getProfileCallback);
        },
        mounted() {
            var self = this;            
            this.$nextTick(function () {
                $(".file-upload-admin").on('change', function () {
                    self.readURL(this);
                });
                $(".upload-button-admin").on('click', function () {
                    $(".file-upload-admin").click();
                });
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