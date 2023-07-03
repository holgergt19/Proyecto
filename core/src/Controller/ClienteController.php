<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use App\Controller\AppController;
use App\Model\Table\ClientesTable;

class ClienteController extends AppController
{

    public function viewClasses(): array
    {
        return [JsonView::class, XmlView::class];
    }
    public function login()
    {
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $password = $this->request->getData('password');

            $clienteTable = TableRegistry::getTableLocator()->get('Cliente');
            $cliente = $clienteTable->find()
                ->where(['email' => $email])
                ->first();

            if ($cliente) {
                // Inicio de sesión exitoso
                $this->Auth->setUser($cliente->toArray());
                return $this->redirect(['action' => 'index']);
            } else {
                // Credenciales inválidas
                $this->Flash->error('Credenciales inválidas. Por favor, intenta nuevamente.');
            }
        }
    }
    public function logout()
    {
        $this->Auth->logout();
        return $this->redirect(['controller' => 'Cliente', 'action' => 'login']);
    }
    public function index()
    {
        $ClientesTable = TableRegistry::getTableLocator()->get('Cliente');
        $cliente = $ClientesTable->find('all')->toArray();

        $cliente = $this->paginate($this->Cliente);

        $this->viewBuilder()->setOption('serialize', ['cliente']);

        $this->RequestHandler->renderAs($this, 'json');

        $this->set(compact('cliente'));
    }


    public function view($id = null)
    {
        $cliente = $this->Cliente->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('cliente'));
    }


    public function add()
    {
        $cliente = $this->Cliente->newEmptyEntity();
        if ($this->request->is('post')) {
            $cliente = $this->Cliente->patchEntity($cliente, $this->request->getData());
            if ($this->Cliente->save($cliente)) {
                $this->Flash->success(__('The cliente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cliente could not be saved. Please, try again.'));
        }
        $this->set(compact('cliente'));
    }


    public function edit($id = null)
    {
        $cliente = $this->Cliente->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $cliente = $this->Cliente->patchEntity($cliente, $this->request->getData());
            if ($this->Cliente->save($cliente)) {
                $this->Flash->success(__('The cliente has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cliente could not be saved. Please, try again.'));
        }
        $this->set(compact('cliente'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cliente = $this->Cliente->get($id);
        if ($this->Cliente->delete($cliente)) {
            $this->Flash->success(__('The cliente has been deleted.'));
        } else {
            $this->Flash->error(__('The cliente could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
