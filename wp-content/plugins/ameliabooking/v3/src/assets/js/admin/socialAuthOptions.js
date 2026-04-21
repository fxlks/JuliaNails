import { settings } from "../../../plugins/settings";

const SocialAuthOptions = {
    responseDataKey: 'data',
    tokenPath: 'data.token',
    token: 'social_token',
    providers: {
        facebook: {
            clientId: settings.socialLogin.facebookAppId,
            redirectUri: window.location.origin,
            responseType: 'code',
            authorizationEndpoint: 'https://www.facebook.com/v19.0/dialog/oauth',
            requiredUrlParams: ['display', 'scope'],
            scope: ['email'],
            display: 'popup',
            oauthType: '2.0'
        }
    }
}

export {
    SocialAuthOptions
}