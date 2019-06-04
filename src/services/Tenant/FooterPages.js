export default (data) =>{

    localStorage.removeItem('privacyPolicy');  
    localStorage.removeItem('cookiePolicy');  
    localStorage.removeItem('termsOfUse');  
   
    //Store CMS pages in Local Storage   
    // if (data.cookie_policy) {
    //     localStorage.setItem('cookiePolicy',JSON.stringify(data.cookie_policy))
    // }

    // if (data.privacy_policy) {
    //     localStorage.setItem('privacyPolicy',JSON.stringify(data.privacy_policy))
    // }

    // if (data.cookie_policy) {
    //     localStorage.setItem('listOfLanguage',JSON.stringify(data.cookie_policy))
    // }

}

