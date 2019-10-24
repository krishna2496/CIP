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

        if (navigator.appVersion.toString().indexOf('.NET') > 0){
            window.navigator.msSaveBlob(blob, fileName);
        } else {
            var link = document.createElementNS('http://www.w3.org/1999/xhtml', 'a');
            link.href = URL.createObjectURL(blob);
            link.download = fileName;
            link.click();
        }
    });
};