<template>
  <div>
    <!-- Styled -->
    {{file}}
    <img src="">
   <!--  <b-form-file
      v-model="file"
      :state="Boolean(file)"
      :capture="Boolean(capture)"
      placeholder="Choose a file or drop it here..."
      drop-placeholder="Drop file here..."
      @change="previewFiles"
    ></b-form-file>
    <div class="mt-3">Selected file: {{ file ? file.name : '' }}</div> -->
   
   <input type="file" id="avatar" @change="previewFiles">
  </div>
</template>

<script>
import {getUserDetail,saveProfile,changeUserPassword,changeProfilePicture,changeCity,saveUserProfile,saveSkill,loadLocaleMessages,country,skill,timezone} from "../services/service";
  export default {
    data() {
      return {
        file: null,
        file2: null,
        capture : true,
        selectedFile : '',
        uploading : ""
      }
    },
    methods : {

changeFiles(files){
    console.log(files)
},

              previewFiles(event) {
            event.preventDefault();

            this.selectedFile = event.target.files[0];

            var obj = this;

            const formData = new FormData();
            console.log(this.selectedFile);
            formData.append('avatar', this.selectedFile, this.selectedFile.name);
            // formData.append('data',JSON.stringify(this.details));

            this.uploading = true;            


    console.log(formData);

              changeProfilePicture(formData).then(response => {
                // if(response.error == true){
                //     this.makeToast("danger",response.message);
                // } else {
                    
                //     this.makeToast("success",response.message);
                //     store.commit("changeAvatar",response.data)
                // }
                this.uploading = false;
                
            })


    //             var config = {
    //                 headers: { 'Content-Type': 'multipart/form-data' }
    //             }
    // // console.log(formData);return false;
    //             Vue.http.post('/account/avatar/update',formData,config)
    //             .then(function (response) {
    //                 if(response.data.status){
    //                     obj.avatar = response.data.avatar;
    //                     // toastr.success("Your profile image uploaded successfuly for review!", 'Success', {
    //                     //     timeOut: 2000,
    //                     //     positionClass: "toast-bottom-right"
    //                     // });
    //                 }else{
    //                     alert("Please try with proper image extension");
    //                 }
    //                 obj.uploading = false;
    //             })
    //             .catch(function (error) {
    //                 console.log(error);
    //                 obj.uploading = false;
    //             });
            }, 
    }
  }
</script>