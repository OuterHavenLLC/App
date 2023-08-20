<?php
 Class Common extends GW {
  function __construct() {
   parent::__construct();
   $this->you = $this->core->Member($this->core->Username());
  }
  /*--BEGIN ChatGRP-SUPPLIED REFERENCE CODE--
  // CLASS
class PollingSystem
{
    private $polls = [];
    private $pollFile = 'polls.json';

    public function __construct()
    {
        $this->loadPolls();
    }

    public function getPolls()
    {
        return $this->polls;
    }

    public function getPollById($id)
    {
        foreach ($this->polls as $poll) {
            if ($poll->getId() === $id) {
                return $poll;
            }
        }
        return null;
    }

    public function vote($pollId, $questionIndex, $optionIndex, $csrfToken)
    {
        // Validate CSRF token
        session_start();
        if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $csrfToken) {
            throw new Exception('Invalid CSRF token');
        }

        // Find the poll by ID
        $poll = $this->getPollById($pollId);
        if ($poll === null) {
            throw new Exception('Invalid poll ID');
        }

        // Validate question index
        if (!is_int($questionIndex) || $questionIndex < 0 || $questionIndex >= $poll->getQuestionCount()) {
            throw new Exception('Invalid question index');
        }

        // Validate option index
        if (!is_int($optionIndex) || $optionIndex < 0 || $optionIndex >= $poll->getOptionCount($questionIndex)) {
            throw new Exception('Invalid option index');
        }

        // Vote for the specified question option
        if (!$poll->vote($questionIndex, $optionIndex)) {
            throw new Exception('Invalid question or option index');
        }

        // Save the updated poll data to the polls file
        $this->savePolls();

        return true;
    }

    private function loadPolls()
    {
        if (file_exists($this->pollFile)) {
            $data = file_get_contents($this->pollFile);
            $polls = json_decode($data, true);
            if (is_array($polls)) {
                foreach ($polls as $pollData) {
                    $poll = new Poll($pollData['id'], $pollData['title'], $pollData['questions']);
                    $this->polls[] = $poll;
                }
            }
        }
    }

    private function savePolls()
    {
        $pollData = array();
        foreach ($this->polls as $poll) {
            $pollData[] = array(
                'id' => $poll->getId(),
                'title' => $poll->getTitle(),
                'questions' => $poll->getQuestions()
            );
        }
        $data = json_encode($pollData);
        if ($data === false) {
            throw new Exception('Error encoding poll data to JSON');
        }
        if (file_put_contents($this->pollFile, $data) === false) {
            throw new Exception('Error writing poll data to file');
        }
    }
}

  // USAGE
// Create a new polling system
$pollingSystem = new PollingSystem();

// Create a new poll
$poll = $pollingSystem->createPoll('What is your favorite color?', array('Red', 'Green', 'Blue'));

// Vote for an option
$poll->vote('Red');

// Get the results of the poll
$results = $poll->getResults();
foreach ($results as $option => $votes) {
    echo $option . ': ' . $votes . ' votes' . "\n";
}
  --END ChatGRP-SUPPLIED REFERENCE CODE--*/
  function __destruct() {
   // DESTROYS THIS CLASS
  }
?>