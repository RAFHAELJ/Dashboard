<template>
    <div class="user-form">
        <h2>Cadastro de Usuário</h2>
        <form @submit.prevent="submitForm">
            <div class="form-group">
                <label for="name">Nome</label>
                <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder="Digite seu nome"
                />
                <div v-if="errors.name" class="invalid-feedback">
                    {{ errors.name }}
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder="Digite seu e-mail"
                />
                <div v-if="errors.email" class="invalid-feedback">
                    {{ errors.email }}
                </div>
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="form-control"
                    :class="{ 'is-invalid': errors.password }"
                    placeholder="Digite sua senha"
                />
                <div v-if="errors.password" class="invalid-feedback">
                    {{ errors.password }}
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirme a Senha</label>
                <input
                    type="password"
                    id="confirmPassword"
                    v-model="form.confirmPassword"
                    class="form-control"
                    :class="{ 'is-invalid': errors.confirmPassword }"
                    placeholder="Confirme sua senha"
                />
                <div v-if="errors.confirmPassword" class="invalid-feedback">
                    {{ errors.confirmPassword }}
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>
</template>
<script>
export default {
    data() {
        return {
            form: {
                name: '',
                email: '',
                password: '',
                confirmPassword: ''
            },
            errors: {}
        };
    },
    methods: {
        validateForm() {
            this.errors = {};

            if (!this.form.name) {
                this.errors.name = 'O nome é obrigatório.';
            }

            if (!this.form.email) {
                this.errors.email = 'O e-mail é obrigatório.';
            } else if (!this.validEmail(this.form.email)) {
                this.errors.email = 'O e-mail não é válido.';
            }

            if (!this.form.password) {
                this.errors.password = 'A senha é obrigatória.';
            } else if (this.form.password.length < 6) {
                this.errors.password = 'A senha deve ter pelo menos 6 caracteres.';
            }

            if (!this.form.confirmPassword) {
                this.errors.confirmPassword = 'A confirmação de senha é obrigatória.';
            } else if (this.form.confirmPassword !== this.form.password) {
                this.errors.confirmPassword = 'As senhas não coincidem.';
            }

            return Object.keys(this.errors).length === 0;
        },
        validEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        },
        submitForm() {
            if (this.validateForm()) {
                // Aqui você pode enviar o formulário para o backend, por exemplo:
                // axios.post('/api/users', this.form).then(...);
                alert('Usuário cadastrado com sucesso!');
            }
        }
    }
};
</script>
<style scoped>
.user-form {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.form-control.is-invalid {
    border-color: #e3342f;
}

.invalid-feedback {
    color: #e3342f;
    font-size: 14px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}
</style>
