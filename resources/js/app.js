require('./bootstrap');

import Vue from 'vue';

import Messages from './components/Messages.vue'
import Form from './components/Form.vue'

Vue.component('dw-messages', Messages);
Vue.component('dw-form', Form);

const app = new Vue({
	el: '#app',
	data: {
		messages: []
	},

	created() {
		this.fetchMessages();

		Echo.private('chat')
		.listen('MessageSent', (e) => {
			this.messages.push({
				message: e.message.message,
				user: e.user
			})
		})
	},

	methods: {
		fetchMessages() {
			axios.get('messages').then(response => {
				this.messages = response.data;
			});
		},

		addMessage(message) {
			this.messages.push(message);

			axios.post('messages', message).then(response => {
				console.log(response.data);
			});
		}
	}
});