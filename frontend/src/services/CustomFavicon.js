import axios from "axios";

export default async() => {
    let apiUrl = process.env.VUE_APP_API_ENDPOINT;
    let getDynamicFaviconUrl = apiUrl + "app/custom-favicon";
    await axios.get(getDynamicFaviconUrl).then(({data: {data: {custom_favicon = false}}}) => {
        // Reject the promise if no custom favicon defined
        if (!custom_favicon) {
            return Promise.reject();
        }

        // Replace the favicon of the page
        document
            .getElementById("favicon")
            .setAttribute("href", custom_favicon);

        return Promise.resolve();
    });
};