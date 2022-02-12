export const state = () => ({
  messages: [],
  loading: false
});

export const mutations = {
  setMessages (state, messages) {
    state.messages = messages
  },

  insertContact (state, contact) {
    let index = state.messages.findIndex(i => i.id == contact.id)
    if (index === -1) {
      state.messages.push({
        ...contact,
        messages: []
      })
    }
  },

  insertContactMessage (state, { contact, message }) {
    let index = state.messages.findIndex(i => i.id == contact.id)
    if (index > -1) {
      state.messages[index].messages.push(message)
    } else {
      state.messages.push({
        ...contact,
        messages: [message]
      })
    }
  },

  setContactMessage (state, { id , data }) {
    let messages = data.data
    if (messages.length > 0) {
      let index = state.messages.findIndex(i => i.id == id)
      if (index > -1) {
        state.messages[index].messages = messages
      } else if (messages.length > 0) {
        state.messages[index] = {
          ...data.contact,
          messages: messages,
        }
      }
    }
  },

  loading (state, status) {
    state.loading = typeof status === 'undefined' ? true : status
  },
}

export const getters = {
  messages: state => {
    return state.messages
  },

  contactMessages: (state) => {
    return function(contact_id) {
      let index = state.messages.findIndex(i => i.id == contact_id)
      if (index > -1) {
        let m = state.messages[index].messages.map(i => i)

        function compare(a, b) {
          return new Date(b.created_datetime) - new Date(a.created_datetime);
        }

        return m.sort(compare);
      } else {
        return []
      }
    }
  },

  contactInfo: (state) => {
    return function(contact_id) {
      let index = state.messages.findIndex(i => i.id == contact_id)
      return index > -1 ? state.messages[index] : null
    }
  },

  loading: state => state.loading
}

export const actions = {
  insertContactMessage ({ commit }, message) {
    commit('insertContactMessage', message);
  },

  async fetchMessages ({ commit }) {
    commit('loading')
    await this.$axios.get('/messages')
      .then((r) => {
        commit('setMessages', r.data.data)
      })
      .finally(() => commit('loading', false))
  },

  async fetchContactMessages ({ commit }, contact_id) {
    commit('loading')
    await this.$axios.get('/messages/'+contact_id)
      .then((r) => {
        commit('setContactMessage', { id: contact_id, data: r.data })
      })
      .finally(() => commit('loading', false))
  }
}
