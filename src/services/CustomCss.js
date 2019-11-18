import axios from "axios";

export default async() => {
    let apiUrl = process.env.VUE_APP_API_ENDPOINT;
    let getDynamicCssUrl = apiUrl + "app/custom-css";
    await axios.get(getDynamicCssUrl).then(response => {
        // document
        //     .getElementById("customCss")
        //     .setAttribute("href", response.data.data.custom_css);
        return new Promise(resolve => {
            setTimeout(() => resolve('I did something'), 1000)
        })
    });
};