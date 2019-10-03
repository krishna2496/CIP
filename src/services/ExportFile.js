import store from "../store";
import axios from "axios";

export default async(exportUrl, fileName) => {
    let url = `${process.env.VUE_APP_API_ENDPOINT}${exportUrl}`;
    await axios({
        url: url,
        responseType: "arraybuffer",
        method: "get",
        headers: {
            token: store.state.token
        }
    }).then(response => {
        let blob = new Blob([response.data], { type: "application/xlsx" });
        let link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = fileName;
        link.click();
    });
};