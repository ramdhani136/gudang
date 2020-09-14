<template>
<div class="row">
    <div class="col-8">
        <div class="card card-default">
            <div class="card-header">Messages</div>
            <div class="card card-body p-0">
                <ul class="list-unstyled" style="height:300px;overflow-y:scroll">
                    <li class="p-2" v-for="(message,index) in messages" :key="index">
                        <strong>{{user.name}}</strong>
                        {{message.message}}
                    </li>
                </ul>
            </div>
            <input @keyup.enter="sendMessage()" v-model="newMessage" type="text" class="form-control" name="message" placeholder="Enter your message">
            <span class="text-muted">User is typing ..</span>
        </div>
    </div>
    <div class="col-4">
        <div class="card card-default">
            <div class="card-header">Active Users</div>
            <div class="card-body">
                <ul>
                    <li class="py2">Ilham Ramdhani</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    props: ['ambiluser'],
    data() {
        return {
            messages: [],
            newMessage: '',
            user: this.ambiluser,
        }
    },
    created() {
        this.FetchMessage();
        Echo.channel('chat')
            .listen('MessageSent', (event) => {
                this.messages.push({
                    message: event.message,
                });
                console.log(this.messages);
            });
    },
    methods: {
        FetchMessage() {
            axios.get("messages")
                .then(res => {
                    this.messages = res.data;
                })
        },
        sendMessage() {
            this.messages.push({
                user: this.user,
                message: this.newMessage
            })
            axios.post("messages", {
                message: this.newMessage
            })
            this.newMessage = ''
        }
    }
}
</script>
