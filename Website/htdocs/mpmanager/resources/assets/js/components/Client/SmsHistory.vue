<template>
  <widget :title="'Sms History ('+smses.length+')' "
  color="green"
  :refresh-button="true"
  :subscriber="subscriber">

          <div id="chat-body" class="chat-body chat-body-scroll" ref="chat">
              <ul>
                  <li
                      class="message"
                      v-for="sms in smses"
                      :key="sms.id"
                      :class="sms.direction === 0 ? 'incomming' : ''"
                  >
                      <img v-if="sms.direction===0" :data-letters="personName" src alt />
                      <img
                          v-else
                          width="48px"
                          src="https://cdn3.iconfinder.com/data/icons/49handdrawing/256x256/user-admin.png"
                          class="online"
                          alt
                      />
                      <div class="message-text">
                          <time>{{sms.created_at}}</time>
                          <a v-if="sms.direction===0" href="javascript:void(0);" class="username">{{personName}}</a>
                          <a v-else href="javascript:void(0);" class="username">System</a>
                          {{ sms.body}}
                      </div>
                  </li>
                  <li v-if="smses.length === 0">There is no sms</li>
              </ul>
          </div>


    <div class="chat-footer">
      <md-field>
        <md-textarea placeholder="Write a message..." v-model="message"></md-textarea>
        <md-button type="submit" class="md-primary btn-save" @click="sendSms">Send it</md-button>
      </md-field>


    </div>


  </widget>
</template>

<script>
import Widget from '../../shared/widget'
import { resources } from '../../resources'
import { EventBus } from '../../shared/eventbus'

export default {
    name: 'SmsHistory',
    components: { Widget },
    props: {
        personId: {
            type: String,
            required: true
        },
        personName: {
            type: String,
            required: true
        }
    },

    mounted() {
        this.getSmsList()
    },
    data() {
        return {
            smses: [],
            message: '',
            subscriber:'customer-sms-history'
        }
    },
    methods: {
        getSmsList() {
            axios.get(resources.sms.list + this.personId).then(response => {
                this.smses = response.data.data
                EventBus.$emit('widgetContentLoaded',this.subscriber,this.smses.length)

                this.scrollDown()
            })
        },
        sendSms() {
            if (this.message.length <= 3) {
                alert('Message should contain more than 3 letters')
                return
            }
            axios
                .post(resources.sms.send, {
                    message: this.message,
                    person_id: this.personId,
                    senderId: this.$store.state.admin.id
                })
                .then(response => {
                    this.smses.push(response.data.data)
                    this.message = ''
                    this.scrollDown()
                })
        },

        scrollDown() {
            let parent = this
            setTimeout(function() {
                let chat = parent.$refs.chat
                chat.scrollTop = chat.scrollHeight
            }, 1000)
        }
    }
}
</script>

<style scoped>
.incomming {
  margin-left: 5px !important;
  padding: 10px;
  background-color: rgba(7, 249, 127, 0.23);
}

.message-text time {
  right: 10px !important;
  top: 5px !important;
}

.incomming img {
  border-left: 0px !important;
}

[data-letters]:before {
  content: attr(data-letters);
  display: inline-block;
  font-size: 1em;
  width: 2.5em;
  height: 2.5em;
  line-height: 2.5em;
  text-align: center;
  border-radius: 50%;
  background: plum;
  vertical-align: middle;
  margin-right: 1em;
  color: white;
}

.modal-mask {
  position: fixed;
  z-index: 1001;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 45%;
  margin: 0px auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 2px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
  transition: all 0.3s ease;
  font-family: Helvetica, Arial, sans-serif;
  max-height: 85%;
  overflow-y: scroll;
}

.modal-header h3 {
  margin-top: 0;
  color: #42b983;
}

.modal-body {
  margin: 20px 0;
}

.modal-default-button {
  float: right;
}

.chat-body-scroll {
  overflow-y: scroll !important;
}
.chat-body {
  background: #fafafa;
  background: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJod…EiIGhlaWdodD0iMSIgZmlsbD0idXJsKCNncmFkLXVjZ2ctZ2VuZXJhdGVkKSIgLz4KPC9zdmc+);
  background: -moz-linear-gradient(top, #fafafa 0, #fff 100%);
  background: -webkit-gradient(
    linear,
    left top,
    left bottom,
    color-stop(0%, #fafafa),
    color-stop(100%, #fff)
  );
  background: -webkit-linear-gradient(top, #fafafa 0, #fff 100%);
  background: -o-linear-gradient(top, #fafafa 0, #fff 100%);
  background: -ms-linear-gradient(top, #fafafa 0, #fff 100%);
  background: linear-gradient(to bottom, #f5fcff 0, #fff 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fafafa', endColorstr='#ffffff', GradientType=0);
  box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.04);
  -moz-box-shadow: inset -2px -2px 5px rgba(0, 0, 0, 0.04);
  display: block;
  height: 270px;
  overflow-y: scroll;
  overflow-x: hidden;
  padding: 10px;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  border: 1px solid #fff;
  border-top: none;
}
.chat-footer {
  border-top: 1px solid rgba(0, 0, 0, 0.1);
  background: rgba(248, 248, 248, 0.9);
  padding: 0 10px 15px;
  position: relative;
  box-sizing: border-box;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
}
.textarea-div {
  background-color: #fff;
  border: 1px solid #ccc;
  border-bottom: none;
  margin: 10px 0 0;
}
</style>
