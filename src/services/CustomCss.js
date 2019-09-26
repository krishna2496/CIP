import axios from 'axios'
export default async () => {
        var apiUrl = process.env.VUE_APP_API_ENDPOINT
        var getDynamicCssUrl = apiUrl + 'app/custom-css';        
        await axios.get(getDynamicCssUrl).then( response => {
            document.getElementById('customCss').setAttribute("href", response.data.data.custom_css);            
        })
       
}