<template>
    <div>
        <h1>Radios</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>MAC</th>
                    <th>Geolocalização</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="radio in radios" :key="radio.id">
                    <td>{{ radio.id }}</td>
                    <td>{{ radio.radio }}</td>
                    <td>{{ radio.mac }}</td>
                    <td>{{ radio.geo }}</td>
                    <td>
                        <button class="btn btn-primary" @click="editRadio(radio.id)">Editar</button>
                        <button class="btn btn-danger" @click="deleteRadio(radio.id)">Deletar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    name: 'Radios',
    data() {
        return {
            radios: []
        }
    },
    created() {
        this.fetchRadios();
    },
    methods: {
        fetchRadios() {
            axios.get('/api/radios')
                .then(response => {
                    this.radios = response.data;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        editRadio(id) {
            // Implementar edição
        },
        deleteRadio(id) {
            axios.delete(`/api/radios/${id}`)
                .then(response => {
                    this.fetchRadios();
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }
}
</script>
