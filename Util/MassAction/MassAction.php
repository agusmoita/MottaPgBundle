<?php

namespace MottaPgBundle\Util\MassAction;

/**
* MassAction
*/
class MassAction
{
	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $confirmationModal;

	/**
	 * @var string
	 */
	private $callback;

	/**
	 * @var array
	 */
	private $parameters;

	/**
	 * @var HttpKernel
	 */
	private $httpKernel;

	/**
	 * @param HttpKernel $httpKernel
	 * @return MassAction
	 */
	public function __construct($httpKernel)
	{
		$this->httpKernel = $httpKernel;

		return $this;
	}

	/**
	 * @param int $i
	 * @param string $title
	 * @param string $callback
	 * @param array $parameters
	 * @param string $confirmationModal
	 * @return MassAction
	 */
	public function create($i, $title, $callback, $parameters, $confirmationModal = '')
	{
		$this->id = 'mass-'.$i;
		$this->title = $title;
		$this->callback = $callback;
		$this->parameters = $parameters;
		$this->confirmationModal = $confirmationModal;

		return $this;
	}

	/**
	 * @param QueryHelper $queryHelper
	 * @param string $queryFunc
	 * @param array $data
	 * @param Request $request
	 * @return Response
	 */
	public function process($queryHelper, $queryFunc, $data, $request)
	{
        $ids = $queryHelper->getIds($data['massIds']);

        $form = isset($data['massForm']) ? new MassActionForm($data['massForm']) : null;

        $path = array_merge(
            array(
                'ids' => $ids,
                'form' => $form,
                '_controller' => $this->getCallback()
            ),
            $this->getParameters()
        );

        $subRequest = $request->duplicate(array(), null, $path);

        return $this->httpKernel->handle($subRequest, \Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
	}

	/**
	 * @return string
	 */
	private function getCallback()
	{
		return $this->callback;
	}

	/**
	 * @return array
	 */
	private function getParameters()
	{
		return $this->parameters;
	}
}
