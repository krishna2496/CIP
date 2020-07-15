import store from '../../store'
import axios from 'axios'

export default async(data) => {
  // Create Password API call with params create_password_token, email, password, password_confirmation
  let responseData = {}
  responseData.error = false;
  let defaultLanguage = '';
  if (store.state.defaultLanguage !== null) {
    defaultLanguage = (store.state.defaultLanguage).toLowerCase();
  }
  await axios({
    url: process.env.VUE_APP_API_ENDPOINT + "app/update-password",
    data,
    method: 'put',
    headers: {
      'X-localization': defaultLanguage
    }
  }).then(response => {
    responseData.message = response.data.message;
  })
    .catch(error => {
      if (error.response.data.errors[0].message) {
        responseData.error = true;
        responseData.message = error.response.data.errors[0].message;
      }
    })
  return responseData;
}