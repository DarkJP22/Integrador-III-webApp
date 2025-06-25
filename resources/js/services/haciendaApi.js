import axios from 'axios';


const haciendaApi = axios.create({
    baseURL: 'https://api.hacienda.go.cr',
    timeout: 1000 * 20, // 20 seconds
});


export default haciendaApi;