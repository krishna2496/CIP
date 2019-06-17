import store from '../../store'
import axios from 'axios'

export default async (data) => {
        axios.defaults.headers.common['X-localization'] = (store.state.defaultLanguage).toLowerCase();
        axios.defaults.headers.common['token'] = store.state.token;
        let responseData;
        await axios.get(process.env.VUE_APP_API_ENDPOINT+"missions?page="+data[0].page)
              .then((response) => {
                responseData = response.data;                      
              })
              .catch(error => {
                 
              })          
              return responseData;
}

